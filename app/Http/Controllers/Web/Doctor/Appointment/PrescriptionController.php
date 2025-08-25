<?php

namespace App\Http\Controllers\Web\Doctor\Appointment;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecordLogVisit;
use App\Models\Medication;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Visit;
use App\Models\WaitingList;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrescriptionController extends Controller
{
    public function createPrescription(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'required|exists:appointments,id',
            'visit_id' => 'required|exists:visits,id',
        ]);

        $prescription = Prescription::updateOrCreate([
            'patient_id' => $request->patient_id,
            'doctor_id' => auth()->user()->doctor->id,
            'appointment_id' => $request->appointment_id,
            'visit_id' => $request->visit_id,
        ], [
            'notes' => null,
        ]);

        // بدل redirect عادي → رجع JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'id' => $prescription->id,
            ]);
        }

        return redirect()->route('doctor.dashboard')
            ->with('prescription_id', $prescription->id)
            ->with('patient_id', $request->patient_id);

    }

    public function addItemForm($prescriptionId)
    {
        $prescription = Prescription::with('items')->findOrFail($prescriptionId);
        return view('doctor.appointments.patients.prescriptions.add-item', compact('prescription'));
    }

    public function addMedicineToPrescription(Request $request, $prescriptionId)
    {
        $validated =
            $request->validate([
                'pre_type' => 'required|in:chronic,current',
                'pre_name' => 'required|string|max:255',
                'pre_scientific' => 'nullable|string|max:255',
                'pre_trade' => 'nullable|string|max:255',
                'pre_start_date' => 'required|date',
                'pre_end_date' => 'nullable|date|after_or_equal:pre_start_date',
                'pre_frequency' => 'required|in:once_daily,twice_daily,three_times_daily,daily,weekly,monthly,yearly',
                'pre_dosage_form' => 'required|in:tablet,capsule,pills,syrup,liquid,drops,sprays,patches,injections,powder',
                'pre_dose' => 'required|numeric|min:0.1|max:1000',
                'pre_timing' => 'required|in:before_food,after_food,morning,evening,morning_evening',
                'instructions' => 'nullable|string',
                'pre_alternatives' => 'nullable|array',
                'pre_alternatives.*' => 'string|max:255',


            ])
        ;


        /* // تحويل string لفاصل ',' إلى array ثم إلى JSON
        $validated['pre_alternatives'] = $validated['pre_alternatives']
            ? json_encode(array_map('trim', explode(',', $validated['pre_alternatives'])))
            : null; */

        $prescription = Prescription::findOrFail($prescriptionId);
        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::with('user')->findOrFail($prescription->patient_id);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي.');
        }


        $visit = $this->getActiveVisitForPatientAndDoctor($prescription->patient_id, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }
        // إذا كان الدواء مزمن، يتم إهمال تاريخ الانتهاء
        if ($validated['pre_type'] === 'chronic') {
            $validated['pre_end_date'] = null;
        }

        // الحسابات التلقائية
        $validated['patient_record_id'] = $record->id;
        $validated['visit_id'] = $visit->id;
        $validated['pre_frequency_value'] = $this->getFrequencyValue($validated['pre_frequency']);
        $validated['pre_quantity_per_dose'] = $validated['pre_dose'];
        $validated['pre_total_quantity'] = $this->calculateTotalQuantity($validated);


        $medication = Medication::firstOrCreate(
            [
                'patient_record_id' => $record->id,
                'med_type' => $validated['pre_type'],
                'med_name' => $validated['pre_name'],
                'med_start_date' => $validated['pre_start_date'],
                'med_end_date' => $validated['pre_end_date'],
                'med_frequency' => $validated['pre_frequency'],
                'med_frequency_value' => $validated['pre_frequency_value'],
                'med_dosage_form' => $validated['pre_dosage_form'],
                'med_dose' => $validated['pre_dose'],
                'med_timing' => $validated['pre_timing'],
                'med_quantity_per_dose' => $validated['pre_quantity_per_dose'],
                'med_prescribed_by_doctor' => Auth::user()->name,
                'med_total_quantity' => $validated['pre_total_quantity'],
                'med_taken_quantity' => 0,

            ]
        );
        $medication->save();

        // حساب الكمية التي تم تناولها حتى الآن
        $this->updateTakenQuantity($medication);

        $record->update(['medications_submitted' => true]);
        $this->logMedicalRecordEdit(
            patientId: $prescription->patient_id,
            visitId: $visit->id
        );
        // dd($validated);
        $item = PrescriptionItem::create([
            'prescription_id' => $prescription->id,
            'medication_id' => $medication->id,
            'pre_type' => $validated['pre_type'],
            'pre_name' => $validated['pre_name'],
            'pre_scientific' => $validated['pre_scientific'],
            'pre_trade' => $validated['pre_trade'],
            'pre_start_date' => $validated['pre_start_date'],
            'pre_end_date' => $validated['pre_end_date'],
            'pre_frequency' => $validated['pre_frequency'],
            'pre_frequency_value' => $validated['pre_frequency_value'],   // ✅ المصححة
            'pre_dosage_form' => $validated['pre_dosage_form'],
            'pre_dose' => $validated['pre_dose'],
            'pre_timing' => $validated['pre_timing'],
            'pre_quantity_per_dose' => $validated['pre_quantity_per_dose'], // ✅ المصححة
            'pre_total_quantity' => $validated['pre_total_quantity'],       // ✅ المصححة
            'pre_taken_quantity' => 0,
            'pre_prescribed_by_doctor' => Auth::user()->name,
            'instructions' => $validated['instructions'] ?? null,
            'pre_alternatives' => isset($validated['pre_alternatives'])
                ? json_encode($validated['pre_alternatives'])
                : null,
        ]);



        return back()->with('status', 'تمت إضافة الدواء بنجاح.');
    }
    public function prescription()
    {
        $doctor = auth()->user()->doctor;

        // إحضار الزيارة النشطة للمريض المرتبط بالطبيب
        $activeVisit = Visit::where('doctor_id', $doctor->id)
            ->where('status', 'active') // حسب حالتك "active" أو "open"
            ->latest()
            ->first();

        if (!$activeVisit) {
            return back()->with('error', 'لا توجد زيارة نشطة حالياً.');
        }

        $prescriptions = Prescription::with([
            'items' => function ($query) use ($doctor, $activeVisit) {
                $query->where('pre_prescribed_by_doctor', $doctor->name)
                    ->where('visit_id', $activeVisit->id);
            }
        ])
            ->where('doctor_id', $doctor->id)
            ->where('visit_id', $activeVisit->id)
            ->latest()
            ->get();

        return view('doctor.appointments.patients.prescriptions.index', compact('prescriptions'));
    }


    protected function logMedicalRecordEdit($patientId, $visitId)
    {
        //dd(auth()->user()->doctor->id);
        MedicalRecordLogVisit::firstOrCreate([
            'patient_id' => $patientId,
            'doctor_id' => auth()->user()->doctor->id,
            'visit_id' => $visitId,
        ], [
            'edited_at' => now(),
        ]);
    }

    private function getFrequencyValue(string $code): float
    {
        return match ($code) {
            'once_daily', 'daily' => 1,
            'twice_daily' => 2,
            'three_times_daily' => 3,
            'twice_weekly' => 2 / 7,
            'weekly' => 1 / 7,
            'monthly' => 1 / 30,
            'yearly' => 1 / 365,
            default => 1, // قيمة افتراضية إذا دخلت قيمة غير معروفة
        };
    }
    private function getFrequencyInfo(string $code): array
    {
        return match ($code) {
            'once_daily', 'daily' => ['value' => 1, 'type' => 'daily'],
            'twice_daily' => ['value' => 2, 'type' => 'daily'],
            'three_times_daily' => ['value' => 3, 'type' => 'daily'],
            'twice_weekly' => ['value' => 2, 'type' => 'weekly'],
            'weekly' => ['value' => 1, 'type' => 'weekly'],
            'monthly' => ['value' => 1, 'type' => 'monthly'],
            'yearly' => ['value' => 1, 'type' => 'yearly'],
            default => ['value' => 1, 'type' => 'daily'],
        };
    }
    public function calculateTotalQuantity(array $data): int
    {
        $start = Carbon::parse($data['pre_start_date']);
        $now = now();

        if ($start->gt($now)) {
            // إذا كان الدواء يبدأ في المستقبل، لا يوجد كمية بعد
            return 0;
        }

        // إذا كان الدواء مؤقت وله end date
        if ($data['pre_type'] === 'current' && !empty($data['pre_end_date'])) {
            $end = Carbon::parse($data['pre_end_date']);
        } else {
            // chronic → بدون end_date، نحسب حتى الآن فقط
            $end = $now;
        }

        $days = $start->diffInDays($end) + 1;
        $perDay = $this->getFrequencyValue($data['pre_frequency']);
        $quantityPerDose = (float) $data['pre_dose'];

        return (int) ceil($days * $perDay * $quantityPerDose);
    }

    public function updateTakenQuantity(Medication $medication): void
    {
        $taken = $medication->calculateTakenQuantity();
        $medication->med_taken_quantity = $taken;
        if ($medication->med_type === 'chronic') {
            $medication->med_total_quantity = $taken;
        }
        $medication->save();
    }
    /**
     * تحديث كل الأدوية دفعة واحدة.
     */
    public function updateAllMedications(): void
    {
        Medication::chunk(100, function ($medications) {
            foreach ($medications as $medication) {
                $this->updateTakenQuantity($medication);
            }
        });
    }
    public function getActiveVisit($patientId)
    {
        return Visit::where('patient_id', $patientId)
            ->where('v_status', 'active')
            ->latest('v_started_at')
            ->first();
    }
    protected function getActiveVisitForPatientAndDoctor($patientId, $doctorId)
    {
        $appointment = Appointment::where('patient_id', $patientId)
            ->where('doctor_id', $doctorId)
            ->where('status', 'confirmed')
            ->where('location_type', 'at_Doctor')
            ->first();

        if (!$appointment)
            return null;

        $waiting = WaitingList::where('appointment_id', $appointment->id)
            ->where('w_status', 'in_progress')
            ->exists();

        if (!$waiting)
            return null;

        return Visit::where('appointment_id', $appointment->id)
            ->where('v_status', 'active')
            ->first();
    }


}
