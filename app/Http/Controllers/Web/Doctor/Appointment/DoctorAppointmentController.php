<?php

namespace App\Http\Controllers\Web\Doctor\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\DoctorMaterial;
use App\Models\DoctorSchedule;
use App\Models\Prescription;
use App\Models\Visit;
use App\Models\WaitingList;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class DoctorAppointmentController extends Controller
{
    //عرض المرضى تبع اليوم
    public function patients()
    {
        $appointments = Appointment::with('patient.user')
            ->where('doctor_id', Auth::user()->doctor->id)
            ->whereDate('date', Carbon::today())
            ->where('status', 'confirmed') // فقط المواعيد المؤكدة
            ->orderBy('start_time')
            ->get();
        return view('doctor.appointments.today-patients', compact('appointments'));
    }
    //المرضى يلي في العيادة
    public function patientsInClinic()
    {
        $doctor = Auth::user()->doctor;
        $doctorId = $doctor->id;
        $today = Carbon::today();

        if (!$this->isDoctorAvailableNow($doctor->id)) {
            abort(403, 'الطبيب ليس في وقت دوامه حالياً.');
        }
        $appointments = Appointment::with('patient.user')
            ->where('doctor_id', Auth::user()->doctor->id)
            ->whereDate('date', Carbon::today())
            ->where('status', 'confirmed') // ✅ هذا الشرط ضروري
            ->where('location_type', 'in_Clinic')
            ->orderBy('start_time')
            ->get();
        $waitingPatients = WaitingList::whereHas('appointment', function ($q) use ($doctorId, $today) {
            $q->where('doctor_id', $doctorId)
                ->whereDate('date', $today)
                ->where('location_type', 'in_Clinic'); // تأكد أن المريض في العيادة فعلاً
        })
            ->where('w_status', 'waiting')
            ->orderBy('w_check_in_time') // ترتيب حسب وقت الدخول الحقيقي
            ->get();

        return view('doctor.appointments.clinic-patients', compact('waitingPatients', 'appointments'));
    }

    public function enterConsultation(Appointment $appointment)
    {
        try {
            // تحقق أن المريض في العيادة
            if ($appointment->location_type !== 'in_Clinic') {
                return back()->with('error', 'المريض غير متواجد في العيادة.');
            }

            // تحقق أن هذا المريض هو أول من ينتظر للدخول عند هذا الطبيب اليوم
            $firstInLine = Appointment::where('doctor_id', $appointment->doctor_id)
                ->whereDate('date', Carbon::today())
                ->where('location_type', 'in_Clinic')
                ->where('status', 'confirmed')
                ->orderBy('start_time')
                ->first();

            if (!$firstInLine || $firstInLine->id !== $appointment->id) {
                return back()->with('error', 'ليس هذا دور هذا المريض بعد.');
            }


            // تحديث جدول الانتظار: الحالة إلى in_progress، وتسجيل وقت الدخول
            $waitingEntry = WaitingList::where('appointment_id', $appointment->id)->first();

            if ($waitingEntry) {
                $waitingEntry->update([
                    'w_status' => 'in_progress',
                    'w_start_time' => now()// وقت بدء المعاينة
                ]);
            }

            // تحديث الموعد: مكان المريض في العيادة عند الطبيب
            $appointment->update([
                'location_type' => 'at_Doctor'
            ]);
            // 🔔 نادِ على المريض التالي (الرابع)
            app(\App\Services\Secertary\Notification\AppointementStatusArrivvedNotificationService::class)->sendReminders(); // 👈 استدعاء تابع التذكير

            // ✅ إنشاء سجل الزيارة
            $visit = Visit::create([
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
                'patient_id' => $appointment->patient_id,
                'v_started_at' => Carbon::now(),
                'v_status' => 'active',
            ]);

            return back()->with('success', 'تم إدخال المريض إلى غرفة المعاينة.');
        } catch (\Exception $e) {
            \Log::error('فشل في إدخال المريض: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إدخال المريض.');
        }

    }
    public function finishVisit(Request $request, $id)
{
    $visit = Visit::with('appointment')->findOrFail($id);

    $request->validate([
        'v_notes'  => 'required|string',
        'v_price'  => 'required|numeric|min:1',
    ]);

    // تحقق من وصفة الطبيب
    $hasPrescription = Prescription::where('visit_id', $visit->id)->exists();
    $isFollowUp = $visit->appointment->type === 'followup';

    if (!$isFollowUp && !$hasPrescription) {
        return back()->withErrors(['error' => 'يجب إدخال وصفة طبية لهذا الموعد.']);
    }

    // تحقق من وجود مواد مستخدمة
    $usedMaterials = DoctorMaterial::where('visit_id', $visit->id)->exists();
    if (!$usedMaterials) {
        return back()->withErrors(['error' => 'لم يتم تسجيل أي مواد مستخدمة.']);
    }

    DB::beginTransaction();

    try {
        $visit->update([
            'v_notes'     => $request->v_notes,
            'v_price'     => $request->v_price,
            'v_status'    => 'in_payment',
            'v_ended_at'  => now(),
        ]);

        // تحديث حالة الموعد
        $visit->appointment->update([
            'status' => 'completed',
        ]);

        // تحديث قائمة الانتظار
        WaitingList::where('appointment_id', $visit->appointment_id)
            ->update([
                'status'    => 'done',
                'end_time'  => now(),
            ]);

        DB::commit();

        return redirect()->route('doctor.dashboard')->with('success', 'تم إنهاء الزيارة بنجاح، بانتظار الدفع.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => 'حدث خطأ أثناء إنهاء الزيارة: ' . $e->getMessage()]);
    }
}

    //✅ 1. إدخال السعر (من الطبيب)

    public function setPrice(Request $request, $id)
    {
        $request->validate([
            'v_price' => 'required|numeric|min:1',
        ]);

        $visit = Visit::findOrFail($id);
        $visit->update([
            'v_price' => $request->v_price,
        ]);

        return back()->with('success', 'تم تحديد سعر الزيارة.');
    }



    //لفحص إن كان الطبيب في وقت دوامه الآن:
    function isDoctorAvailableNow($doctorId)
    {
        $now = Carbon::now();
        $today = $now->format('l'); // Sunday, Monday, etc.
        $currentTime = $now->format('H:i:s');

        return DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day', $today)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->exists();
    }
}
