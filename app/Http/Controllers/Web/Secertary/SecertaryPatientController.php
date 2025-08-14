<?php

namespace App\Http\Controllers\Web\Secertary;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Visit;
use Illuminate\Http\Request;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
class SecertaryPatientController extends Controller
{
    public function patients()
    {
        $patients = Patient::with(['user', 'patient_record', 'appointments'])
            ->get()
            ->map(function ($patient) {
                $completedRecord = $patient->patient_record
                    ? collect([
                        $patient->patient_record->profile_submitted,
                        $patient->patient_record->diseases_submitted,
                        $patient->patient_record->operations_submitted,
                        $patient->patient_record->medicalAttachments_submitted,
                        $patient->patient_record->allergies_submitted,
                        $patient->patient_record->family_history_submitted,
                        $patient->patient_record->medications_submitted,
                        $patient->patient_record->medicalfiles_submitted,
                    ])->every(fn($val) => $val === 1)
                    : false;

                return [
                    'id' => $patient->id,
                    'name' => $patient->user->name,
                    'created_at' => $patient->created_at,
                    'email' => $patient->user->email,
                    'phone' => $patient->user->phone,
                    'photo' => $patient->photo,
                    'record_completed' => $completedRecord,
                    'appointments_count' => $patient->appointments->whereIn('status', ['completed', 'canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary',])->count(),
                ];
            });

        return view('secretary.patient.patient', compact('patients'));
    }

    public function patient_show($id)
    {
        $patient = Patient::findOrFail($id);

        // 📅 آخر زيارة
        $lastVisit = Visit::where('patient_id', $patient->id)
            ->orderByDesc('v_started_at')
            ->first();

        // ✅ الزيارات المكتملة مع كل التفاصيل
        $completedVisits = Visit::where('patient_id', $patient->id)
            ->where('v_status', 'completed')
            ->orderByDesc('v_started_at')
            ->get();

        // ❌ المواعيد الملغاة
        $cancelledAppointments = Appointment::where('patient_id', $patient->id)
            ->whereIn('status', ['canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary'])
            ->orderByDesc('date')
            ->get();

        // ⏳ المواعيد التي تنتظر التأكيد
        $pendingAppointments = Appointment::where('patient_id', $patient->id)
            ->where('status', 'pending')
            ->orderBy('date')
            ->get();

        // ✅ عدد الزيارات المكتملة (لإظهار العدد)
        $completedVisitsCount = $completedVisits->count();

        // ❌ عدد المواعيد الملغاة (لإظهار العدد)
        $cancelledAppointmentsCount = $cancelledAppointments->count();

        // ✨ تحقق هل للمريض موعد اليوم
        $today = now()->toDateString();
        $todayAppointment = Appointment::where('patient_id', $patient->id)
            ->whereDate('date', $today)
            ->whereNotIn('status', ['canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary'])
            ->first();

        // ✨ أقرب موعد مستقبلي إذا ما عنده موعد اليوم
        $nextAppointment = null;
        if (!$todayAppointment) {
            $nextAppointment = Appointment::where('patient_id', $patient->id)
                ->whereDate('date', '>', $today)
                ->whereNotIn('status', ['canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary'])
                ->orderBy('date')
                ->first();
        }

        $medicalRecord = $patient->patient_record
            ? collect([
                $patient->patient_record->profile_submitted,
                $patient->patient_record->diseases_submitted,
                $patient->patient_record->operations_submitted,
                $patient->patient_record->medicalAttachments_submitted,
                $patient->patient_record->allergies_submitted,
                $patient->patient_record->family_history_submitted,
                $patient->patient_record->medications_submitted,
                $patient->patient_record->medicalfiles_submitted,
            ])->every(fn($val) => $val === 1)
            : false;

        return view('secretary.patient.patient-show', compact(
            'patient',
            'lastVisit',
            'completedVisits',
            'completedVisitsCount',
            'cancelledAppointments',
            'cancelledAppointmentsCount',
            'pendingAppointments',
            'medicalRecord',
            'todayAppointment',
            'nextAppointment'
        ));
    }


    public function patient_delete($id)
    {
        $patient = Patient::findOrFail($id);
        dd($patient);
        $patient->delete();
        return redirect()->route('secretary.patients')->with('success', 'تم حذف المريض بنجاح');

    }




}
