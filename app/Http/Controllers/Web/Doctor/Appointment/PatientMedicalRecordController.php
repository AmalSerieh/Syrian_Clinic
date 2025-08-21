<?php

namespace App\Http\Controllers\Web\Doctor\Appointment;

use App\Http\Controllers\Controller;
use App\Models\MedicalAttachment;
use App\Models\MedicalFile;
use App\Models\Allergy;
use App\Models\Appointment;
use App\Models\Disease;
use App\Models\MedicalRecordLogVisit;
use App\Models\Medication;
use App\Models\Operation;
use App\Models\Patient;
use App\Models\Patient_profile;
use App\Models\Patient_record;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class PatientMedicalRecordController extends Controller
{


    public function show(Patient $patient)
    {
        $doctor = Auth::user()->doctor;

        // تحقق من وجود موعد سابق أو حالي بين الطبيب وهذا المريض
        $hasAppointment = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'completed']) // أضف الحالات التي تسمح بالرؤية
            ->whereIn('location_type', ['in_Home', 'on_Street', 'in_Clinic', 'at_Doctor', 'in_Payment', 'finished'])
            ->exists();

        if (!$hasAppointment) {
            abort(Response::HTTP_FORBIDDEN, 'ليس لديك إذن لرؤية سجل هذا المريض.');
        }

        $record = $patient->patient_record()->with([
            'patient_profile',
            'diseases',
            'medications',
            'operations',
            'allergies',
            'medicalAttachment',
            'medicalFiles',
        ])->first();

        return view('doctor.appointments.patients.medical-record', compact('record', 'patient'));
    }
    public function patient_profile($patient_record_id)
    {
        $doctor = Auth::user()->doctor;

        // 1. جلب السجل الطبي والتحقق من وجوده
        $patientRecord = Patient_record::with('patient')->findOrFail($patient_record_id);
        if (!$patientRecord) {
            return redirect()->back()->with('warning', 'لا يوجد سجل طبي لهذا المريض.');
        }
        $patient = $patientRecord->patient;


        $recordId = $patient_record_id;

        // 2. التحقق من وجود موعد حالي/سابق مع المريض
        $hasAppointment = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$hasAppointment) {
            abort(Response::HTTP_FORBIDDEN, 'ليس لديك إذن لرؤية هذا القسم.');
        }

        // 3. جلب الملف الشخصي المرتبط بالسجل
        $patientProfile = $patientRecord->patient_profile;

        if (!$patientProfile) {
            // في حال لا يوجد ملف بعد
            return redirect()->back()->with('warning', 'لا يوجد ملف شخصي طبي لهذا المريض بعد.');
        }

        // 4. عرض الملف
        return view('doctor.appointments.patients.medical-record.patient-profile.show', [
            'patientProfile' => $patientProfile,
            'patient' => $patient
        ]);
    }
    public function patient_profile_Edit($id)
    {
        $patientProfile = Patient_profile::findOrFail($id);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $patientProfile->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا تملك صلاحية التعديل على هذا الملف (لا توجد زيارة نشطة حالياً أو المريض ليس قيد المعاينة).');
        }

        return view('doctor.appointments.patients.medical-record.patient-profile.edit', compact('patientProfile'));
    }

    public function patient_profile_Update(Request $request, $id)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'gender' => 'required|string|in:male,female',
            'date_birth' => 'required|date|before:today',
            'height' => 'required|numeric|min:1',
            'weight' => 'required|numeric|min:1',
            'blood_type' => 'required|string|in:A+,B+,O+,AB+,A-,B-,O-,AB-',
            'smoker' => 'required|boolean',
            'alcohol' => 'required|boolean',
            'drug' => 'required|boolean',
            'matital_status' => 'required|string|in:single,married,widower,divorced',
        ]);

        // جلب الملف
        $patientProfile = Patient_profile::findOrFail($id);

        // التحقق من الصلاحيات يدويًا
        $doctorId = auth()->user()->doctor->id;

        $hasAppointment = Appointment::where('patient_id', $patientProfile->patientRecord->patient_id)
            ->where('doctor_id', $doctorId)
            ->whereIn('status', ['confirmed'])
            ->exists();
        if (!$hasAppointment) {
            abort(403, 'لا تملك صلاحية التعديل على هذا الملف.');
        }



        $visit = Visit::where('appointment_id', function ($q) use ($doctorId, $patientProfile) {
            $q->select('id')
                ->from('appointments')
                ->where('patient_id', $patientProfile->patientRecord->patient_id)
                ->where('doctor_id', $doctorId)
                ->where('location_type', 'at_Doctor')
                ->where('status', 'confirmed')
                ->limit(1);
        })
            ->where('v_status', 'active')
            ->first();

        if (!$visit) {
            abort(403, 'لا تملك صلاحية التعديل على هذا الملف (لا توجد زيارة نشطة حالياً).');
        }
        // التحديث و حفظ
        $validated['visit_id'] = $visit->id;
        $patientProfile->update($validated);


        /* $patientProfile->update(array_merge($validated, [
            'visit_id' => $visit->id,
        ])); */
        $this->logMedicalRecordEdit(
            patientId: $patientProfile->patientRecord->patient_id,
            visitId: $visit->id
        );



        return redirect()
            ->route('doctor.medical-record.patient_profile', $patientProfile->patient_record_id)
            ->with('success', '✅ تم تعديل الملف الطبي بنجاح.');
    }
    public function patient_profile_Create($patientId)
    {
        $user = auth()->user();
        $doctorId = $user->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId); // تأكد من جلب العلاقة

        // تحقق من أن المستخدم طبيب
        if (!$user->isDoctor()) {
            abort(403, 'Unauthorized');
        }

        // تحقق من وجود سجل طبي
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'no have record');
        }

        // تحقق من أن السكرتيرة أنشأت الحساب وليس فيه ملف طبي
        $secretary = $patient->user->created_by == 'secretary';
        if (!$secretary) {
            return redirect()->back()->withErrors(['msg' => 'لا تملك صلاحية إنشاء الملف الطبي لهذا المريض.']);
        }
        if ($record->patient_profile || $record->profile_submitted) {
            return redirect()->back()->with('error', 'message.profile_already_submitted');
        }

        // تحقق من وجود موعد مؤكد بين المريض والطبيب
        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا تملك صلاحية التعديل على هذا الملف (لا توجد زيارة نشطة حالياً أو المريض ليس قيد المعاينة).');
        }

        return view('doctor.appointments.patients.medical-record.patient-profile.create', compact('patient'));
    }

    public function patient_profile_Store(Request $request, $patientId)
    {
        // ✅ التحقق من صحة البيانات
        $validated = $request->validate([
            'gender' => 'required|string|in:male,female',
            'date_birth' => 'required|date|before:today',
            'height' => 'required|numeric|min:1',
            'weight' => 'required|numeric|min:1',
            'blood_type' => 'required|string|in:A+,B+,O+,AB+,A-,B-,O-,AB-',
            'smoker' => 'required|boolean',
            'alcohol' => 'required|boolean',
            'drug' => 'required|boolean',
            'matital_status' => 'required|string|in:single,married,widower,divorced',
        ]);

        $doctor = auth()->user()->doctor;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;


        if (!$record || $record->patient_profile) {
            return redirect()->back()->with('error', 'message.profile_already_submitted');
        }
        // تحقق من وجود موعد مؤكد بين المريض والطبيب
        // تحقق من وجود موعد مؤكد
        $appointment = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->latest()
            ->first();

        if (!$appointment) {
            return redirect()->back()->with('error', trans('message.no_appointment_with_patient'));
        }

        // تحقق من وجود زيارة نشطة
        $visit = Visit::where('appointment_id', $appointment->id)
            ->where('v_status', 'active')
            ->first();

        if (!$visit) {
            return redirect()->back()->with('error', 'لا توجد زيارة نشطة حالياً.');
        }



        // ✅ إنشاء الملف الطبي مع ربط الزيارة والطبيب
        $profile = new Patient_profile(array_merge($validated, [
            'patient_record_id' => $record->id,
            'visit_id' => $visit->id,
        ]));
        $profile->save();

        // تحديث حالة السجل
        $record->update(['profile_submitted' => true]);

        // ✅ تسجيل في سجل التعديلات العامة
        $this->logMedicalRecordEdit(
            patientId: $patient->id,
            visitId: $visit->id
        );


        return redirect()->route('doctor.medical-record.patient_profile')->with('success', trans('message.created_successfully'));
    }


    public function diseases($patient_record_id)
    {
        $doctor = Auth::user()->doctor;

        // 1. جلب السجل الطبي والتحقق من وجوده
        $patientRecord = Patient_record::with('patient')->findOrFail($patient_record_id);
        $patient = $patientRecord->patient;
        if (!$patientRecord) {
            return redirect()->back()->with('warning', 'لا يوجد سجل طبي لهذا المريض.');
        }

        // 2. التحقق من وجود موعد حالي/سابق مع المريض
        $hasAppointment = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$hasAppointment) {
            abort(Response::HTTP_FORBIDDEN, 'ليس لديك إذن لرؤية هذا القسم.');
        }

        // 3. جلب الأمراض
        $diseases = $patientRecord->diseases;

        $current = $diseases->where('d_type', 'current');
        $chronic = $diseases->where('d_type', 'chronic');

        if (!$diseases) {
            // في حال لا يوجد ملف بعد
            return redirect()->back()->with('warning', 'لا يوجد أمراض  لهذا المريض بعد.');
        }

        // 4. عرض الملف
        return view('doctor.appointments.patients.medical-record.diseases.show', [
            'diseases' => $diseases,
            'current' => $current,
            'chronic' => $chronic,
            'patient' => $patient
        ]);
    }

    public function diseases_Edit($diseaseId)
    {
        $disease = Disease::findOrFail($diseaseId);
        $doctorId = auth()->user()->doctor->id;

        $patientId = $disease->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا تملك صلاحية التعديل على هذا الملف (لا توجد زيارة نشطة حالياً أو المريض ليس قيد المعاينة).');
        }

        return view('doctor.appointments.patients.medical-record.diseases.edit', compact('disease'));
    }

    public function diseases_Update(Request $request, $diseaseId)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'id' => 'required|exists:diseases,id',
            'd_type' => 'required|in:current,chronic',
            'd_name' => 'required|string',
            'd_diagnosis_date' => 'required|date',
            'd_doctor' => 'nullable|string',
            'd_advice' => 'nullable|string',
            'd_prohibitions' => 'nullable|string',
        ]);

        // جلب الملف
        $disease = Disease::findOrFail($diseaseId);

        // التحقق من الصلاحيات يدويًا
        $doctorId = auth()->user()->doctor->id;

        $patientId = $disease->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا تملك صلاحية التعديل على هذا الملف (لا توجد زيارة نشطة حالياً).');
        }

        // التحديث
        $validated['visit_id'] = $visit->id;
        $disease->update($validated);

        //
        $this->logMedicalRecordEdit(
            patientId: $patientId,
            visitId: $visit->id
        );

        return redirect()
            ->route('doctor.medical-record.diseases', $disease->patient_record_id)
            ->with('success', '✅ تم تعديل الملف الطبي بنجاح.');
    }
    public function diseases_Create($patientId)
    {
        $user = auth()->user();
        $doctorId = $user->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي للمريض.');
        }

        // تحقق من أن المستخدم طبيب
        if (!$user->isDoctor()) {
            abort(403, 'Unauthorized');
        }

        // تحقق من أن السكرتيرة أنشأت الحساب وليس فيه ملف طبي
        /*  $secretary = $patient->user->created_by == 'secretary';
         if (!$secretary) {
             return redirect()->back()->withErrors(['msg' => 'لا تملك صلاحية إنشاء الملف الطبي لهذا المريض.']);
         }
         if ($record->diseases || $record->diseases_submitted) {
             return redirect()->back()->with('error', 'message.profile_already_submitted');
         }*/


        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }
        // تحقق من وجود موعد مؤكد بين المريض والطبيب


        return view('doctor.appointments.patients.medical-record.diseases.create', compact('patient'));
    }

    public function diseases_Store(Request $request, $patientId)
    {
        // ✅ التحقق من صحة البيانات
        $validated = $request->validate([
            'd_type' => 'required|in:current,chronic',
            'd_name' => 'required|string',
            'd_diagnosis_date' => 'required|date',
            'd_doctor' => 'nullable|string',
            'd_advice' => 'nullable|string',
            'd_prohibitions' => 'nullable|string',
        ]);
        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }


        $disease = new Disease(array_merge($validated, [
            'patient_record_id' => $record->id,
            'visit_id' => $visit->id
        ]));


        $disease->save();

        $record->update(['diseases_submitted' => true]);

        $this->logMedicalRecordEdit(
            patientId: $patientId,
            visitId: $visit->id
        );

        return redirect()->route('doctor.medical-record.diseases', $record->id)->with('success', trans('message.created_successfully'));
    }
    public function diseases_Delete(Disease $disease)
    {
        $doctorId = auth()->user()->doctor->id;
        $patientId = $disease->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        $disease->delete();

        $this->logMedicalRecordEdit(
            patientId: $patientId,
            visitId: $visit->id
        );

        return redirect()->back()->with('success', 'تم حذف المرض بنجاح.');
    }

    public function diseases_DeleteAll($patientRecordId)
    {
        $doctorId = auth()->user()->doctor->id;

        // جلب السجل الطبي والتأكد من وجوده
        $patientRecord = Patient_record::findOrFail($patientRecordId);
        $patientId = $patientRecord->patient_id;

        // التحقق من وجود زيارة نشطة
        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا تملك صلاحية الحذف (لا توجد زيارة نشطة حالياً).');
        }

        // حذف جميع الأمراض المرتبطة بالسجل الطبي
        $patientRecord->diseases()->delete();

        // تسجيل التعديل في سجل التعديلات
        $this->logMedicalRecordEdit(
            patientId: $patientId,
            visitId: $visit->id
        );

        return redirect()
            ->back()
            ->with('success', '✅ تم حذف جميع الأمراض بنجاح.');
    }

    public function medications($patient_record_id)
    {
        $doctor = auth()->user()->doctor;
        $patientRecord = Patient_record::with('patient')->findOrFail($patient_record_id);
        $patient = $patientRecord->patient;

        if (!$patientRecord) {
            return redirect()->back()->with('warning', 'لا يوجد سجل طبي لهذا المريض.');
        }

        $hasAppointment = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$hasAppointment) {
            abort(403, 'ليس لديك إذن لرؤية هذا القسم.');
        }

        $medications = $patientRecord->medications;

        if ($medications->isEmpty()) {
            return back()->with(['message' => trans('message.not_filled_yet')]);
        }

        // 🔁 صياغة شكل الدواء حسب المطلوب
        $formatMedication = function ($med) {
            return [
                'id' => $med->id,
                'med_name' => $med->med_name,
                'med_type' => $med->med_type,
                'start_date' => $med->med_start_date,
                'end_date' => $med->med_end_date,
                'frequency' => $med->med_frequency,
                'med_frequency_value' => $med->med_frequency_value,
                'dosage_form' => $med->med_dosage_form,
                'dose' => $med->med_dose,
                'quantity_per_dose' => $med->med_quantity_per_dose,
                'timing' => $med->med_timing,
                'med_total_quantity' => intval($med->med_total_quantity),
                'med_prescribed_by_doctor' => $med->med_prescribed_by_doctor,
                'is_active' => $med->is_active,

                'taken_till_now' => $med->med_type === 'chronic'
                    ? $med->calculateTakenQuantity()
                    : $med->calculateProgressDetailed()['taken_till_now'],

                'progress_info' => $med->calculateProgressDetailed(),
                'progress_percent % ' => $med->med_total_quantity > 0
                    ? round(($med->med_taken_quantity / $med->med_total_quantity) * 100, 2)
                    : null,
            ];
        };

        $current = $medications->where('med_type', 'current')->map($formatMedication);
        $chronic = $medications->where('med_type', 'chronic')->map($formatMedication);

        return view('doctor.appointments.patients.medical-record.medications.show', compact(
            'patient',
            'current',
            'chronic'
        ));
    }

    public function medications_Create($patientId)
    {
        $user = auth()->user();
        $doctorId = $user->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي للمريض.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        return view('doctor.appointments.patients.medicalRecord.medications.create', compact('patient'));
    }

    public function medications_Store(Request $request, $patientId)
    {
        $validated = $request->validated([
            'med_type' => 'required|in:chronic,current',
            'med_name' => 'required|string|max:255',
            'med_start_date' => 'required|date',
            'med_end_date' => 'nullable|date|after_or_equal:med_start_date',
            'med_frequency' => 'required|in:once_daily,twice_daily,three_times_daily,daily,weekly,monthly,yearly',
            'med_dosage_form' => 'required|in:tablet,capsule,pills,syrup,liquid,drops,sprays,patches,injections',
            'powder',
            'med_dose' => 'required|numeric|min:0.1|max:1000',
            'med_timing' => 'nullable|in:before_food,after_food,morning,evening,morning_evening',
            'med_prescribed_by_doctor' => 'nullable|string|max:255',
        ]);
        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي.');
        }


        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }
        // إذا كان الدواء مزمن، يتم إهمال تاريخ الانتهاء
        if ($validated['med_type'] === 'chronic') {
            $validated['med_end_date'] = null;
        }

        // الحسابات التلقائية
        $validated['patient_record_id'] = $record->id;
        $validated['visit_id'] = $visit->id;
        $validated['med_frequency_value'] = $this->getFrequencyValue($validated['med_frequency']);
        $validated['med_quantity_per_dose'] = $validated['med_dose'];
        $validated['med_total_quantity'] = $this->calculateTotalQuantity($validated);
        $validated['med_taken_quantity'] = 0;



        // إنشاء الدواء
        $medication = Medication::create($validated);

        $medication->save();

        // حساب الكمية التي تم تناولها حتى الآن
        $this->updateTakenQuantity($medication);

        $record->update(['medications_submitted' => true]);

        $this->logMedicalRecordEdit(
            patientId: $patientId,
            visitId: $visit->id
        );

        return redirect()->route('doctor.medical-record.medications', $record->id)
            ->with('success', 'تمت إضافة الدواء بنجاح.');
    }

    public function medications_Edit($medicationId)
    {
        $medication = Medication::findOrFail($medicationId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $medication->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا تملك صلاحية التعديل على هذا الملف (لا توجد زيارة نشطة حالياً).');
        }

        return view('doctor.appointments.patients.medicalRecord.medications.edit', compact('medication'));
    }

    public function medications_Update(Request $request, $medicationId)
    {
        $validated = $request->validate([
            'med_type' => 'required|in:chronic,current',
            'med_name' => 'required|string|max:255',
            'med_start_date' => 'required|date',
            'med_end_date' => 'nullable|date|after_or_equal:med_start_date',
            'med_frequency' => 'required|in:once_daily,twice_daily,three_times_daily,daily,weekly,monthly,yearly',
            'med_dosage_form' => 'required|in:tablet,capsule,pills,syrup,liquid,drops,sprays,patches,injections',
            'powder',
            'med_dose' => 'required|numeric|min:0.1|max:1000',
            'med_timing' => 'nullable|in:before_food,after_food,morning,evening,morning_evening',
            'med_prescribed_by_doctor' => 'nullable|string|max:255',
        ]);
        $medication = Medication::findOrFail($medicationId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $medication->patientRecord->patient_id;
        $record = $medication->patientRecord;


        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا تملك صلاحية التعديل على هذا الملف (لا توجد زيارة نشطة حالياً).');
        }

        // إذا كان الدواء مزمن، يتم تجاهل med_end_date
        if ($validated['med_type'] === 'chronic') {
            $validated['med_end_date'] = null;
        }

        // الحقول المحسوبة
        $validated['visit_id'] = $visit->id;
        $validated['med_frequency_value'] = $this->getFrequencyValue($validated['med_frequency']);
        $validated['med_quantity_per_dose'] = $validated['med_dose'];
        $validated['med_total_quantity'] = $this->calculateTotalQuantity($validated);

        // التحديث
        $medication->update($validated);

        // تحديث الكمية المتناولة فعلياً
        $this->updateTakenQuantity($medication);

        // تأكيد تعديل حالة "تم إدخال أدوية"
        $record->update(['medications_submitted' => true]);

        $this->logMedicalRecordEdit(
            patientId: $patientId,
            visitId: $visit->id
        );

        return redirect()
            ->route('doctor.medical-record.medications', $medication->patient_record_id)
            ->with('success', '✅ تم تعديل الدواء بنجاح.');
    }

    public function medications_Delete($medicationId)
    {
        $medication = Medication::findOrFail($medicationId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $medication->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, '❌ لا تملك صلاحية الحذف (لا توجد زيارة نشطة حالياً).');
        }

        $medication->delete();

        $this->logMedicalRecordEdit(
            patientId: $patientId,
            visitId: $visit->id
        );

        return redirect()
            ->route('doctor.medical-record.medications', $medication->patient_record_id)
            ->with('success', '🗑️ تم حذف الدواء بنجاح.');
    }

    public function medications_DeleteAll($patientRecordId)
    {
        $patientRecord = Patient_record::findOrFail($patientRecordId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, '❌ لا يمكنك حذف الأدوية بدون وجود زيارة نشطة مع هذا المريض.');
        }

        // حذف كل الأدوية المرتبطة بالسجل
        $patientRecord->medications()->delete();

        // تسجيل التعديل في سجل الزيارات
        $this->logMedicalRecordEdit(
            patientId: $patientId,
            visitId: $visit->id
        );

        return redirect()
            ->route('doctor.medical-record.medications', $patientRecordId)
            ->with('success', '✅ تم حذف جميع الأدوية بنجاح.');
    }

    public function medication_show($id)
    {
        $medication = Medication::with('patientRecord.patient')->findOrFail($id);

        $doctorId = auth()->user()->doctor->id;
        $patientId = $medication->patientRecord->patient_id;

        // التحقق من وجود زيارة نشطة أو منتهية للطبيب مع هذا المريض
        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, '❌ لا يمكنك عرض بيانات هذا الدواء بدون وجود زيارة نشطة أو منتهية مع المريض.');
        }

        return view('doctor.medical-record.medications.show', compact('medication'));
    }



    public function operations($patient_record_id)
    {
        $doctor = Auth::user()->doctor;

        // جلب السجل الطبي للمريض
        $patientRecord = Patient_record::with('patient')->findOrFail($patient_record_id);
        $patient = $patientRecord->patient;

        if (!$patientRecord) {
            return redirect()->back()->with('warning', 'لا يوجد سجل طبي لهذا المريض.');
        }

        // التأكد من وجود موعد سابق أو حالي
        $hasAppointment = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$hasAppointment) {
            abort(Response::HTTP_FORBIDDEN, 'ليس لديك إذن لرؤية هذا القسم.');
        }

        $operations = $patientRecord->operations;

        return view('doctor.appointments.patients.medicalRecord.operations.show', [
            'operations' => $operations,
            'patient' => $patient,
        ]);
    }
    public function operations_Create($patientId)
    {
        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي للمريض.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        return view('doctor.appointments.patients.medicalRecord.operations.create', compact('patient'));
    }
    public function operations_Store(Request $request, $patientId)
    {
        $validated = $request->validate([
            'op_name' => ['required', 'string'],
            'op_doctor_name' => ['required', 'string'],
            'op_hospital_name' => ['required', 'string'],
            'op_date' => ['required', 'date'],
        ]);

        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        $record->operations()->create(array_merge($validated, [
            'visit_id' => $visit->id,
        ]));

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->route('doctor.medical-record.operations', $record->id)
            ->with('success', '✅ تم حفظ العملية بنجاح.');
    }

    public function operations_Edit($operationId)
    {
        $operation = Operation::findOrFail($operationId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $operation->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        return view('doctor.appointments.patients.medicalRecord.operations.edit', compact('operation'));
    }
    public function operations_Update(Request $request, $operationId)
    {
        $validated = $request->validate([
            'op_name' => ['required', 'string'],
            'op_doctor_name' => ['required', 'string'],
            'op_hospital_name' => ['required', 'string'],
            'op_date' => ['required', 'date'],
        ]);

        $operation = Operation::findOrFail($operationId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $operation->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        $validated['visit_id'] = $visit->id;
        $operation->update($validated);

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()
            ->route('doctor.medical-record.operations', $operation->patient_record_id)
            ->with('success', '✅ تم تعديل العملية بنجاح.');
    }

    public function operations_Delete(Operation $operation)
    {
        $doctorId = auth()->user()->doctor->id;
        $patientId = $operation->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        $operation->delete();

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->back()->with('success', '✅ تم حذف العملية بنجاح.');
    }


    public function operations_DeleteAll($patientRecordId)
    {
        $doctorId = auth()->user()->doctor->id;
        $patientRecord = Patient_record::findOrFail($patientRecordId);
        $patientId = $patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا تملك صلاحية الحذف (لا توجد زيارة نشطة حالياً).');
        }

        $patientRecord->operations()->delete();

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->back()->with('success', '✅ تم حذف جميع العمليات بنجاح.');
    }

    public function operations_show($operationId)
    {
        $operation = Operation::with('patientRecord.patient')->findOrFail($operationId);

        $doctorId = auth()->user()->doctor->id;
        $patientId = $operation->patientRecord->patient_id;

        // التحقق من وجود زيارة نشطة أو منتهية للطبيب مع هذا المريض
        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, '❌ لا يمكنك عرض بيانات هذا الدواء بدون وجود زيارة نشطة أو منتهية مع المريض.');
        }

        return view('doctor.medical-record.operations.show', compact('operation'));
    }

    public function allergies($patient_record_id)
    {
        $doctor = Auth::user()->doctor;
        $patientRecord = Patient_record::with('patient')->findOrFail($patient_record_id);
        $patient = $patientRecord->patient;

        if (!$patientRecord) {
            return redirect()->back()->with('warning', 'لا يوجد سجل طبي لهذا المريض.');
        }

        $hasAppointment = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$hasAppointment) {
            abort(Response::HTTP_FORBIDDEN, 'ليس لديك إذن لرؤية هذا القسم.');
        }

        $allergies = $patientRecord->allergies;

        return view('doctor.appointments.patients.medicalRecord.allergies.show', compact('allergies', 'patient'));
    }

    public function allergies_Create($patientId)
    {
        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي للمريض.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        return view('doctor.appointments.patients.medicalRecord.allergies.create', compact('patient'));
    }

    public function allergies_Store(Request $request, $patientId)
    {
        $validated = $request->validate([
            'aller_power' => ['required', 'string', 'in:strong,medium,weak'],
            'aller_name' => ['required', 'string'],
            'aller_type' => ['required', 'string', 'in:animal,pollen,Food,dust,mold,medicine,seasons,other'],
            'aller_cause' => ['nullable', 'string'],
            'aller_treatment' => ['nullable', 'string'],
            'aller_pervention' => ['nullable', 'string'],
            'aller_reasons' => ['nullable', 'string'],
        ]);

        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        $record->allergies()->create(array_merge($validated, [
            'visit_id' => $visit->id,
        ]));

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->route('doctor.medical-record.allergies', $record->id)
            ->with('success', '✅ تم حفظ الحساسية بنجاح.');
    }

    public function allergies_Edit($allergyId)
    {
        $allergy = Allergy::findOrFail($allergyId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $allergy->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        return view('doctor.appointments.patients.medicalRecord.allergies.edit', compact('allergy'));
    }

    public function allergies_Update(Request $request, $allergyId)
    {
        $validated = $request->validate([
            'aller_power' => ['required', 'string', 'in:strong,medium,weak'],
            'aller_name' => ['required', 'string'],
            'aller_type' => ['required', 'string', 'in:animal,pollen,Food,dust,mold,medicine,seasons,other'],
            'aller_cause' => ['nullable', 'string'],
            'aller_treatment' => ['nullable', 'string'],
            'aller_pervention' => ['nullable', 'string'],
            'aller_reasons' => ['nullable', 'string'],
        ]);

        $allergy = Allergy::findOrFail($allergyId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $allergy->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        $validated['visit_id'] = $visit->id;
        $allergy->update($validated);

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()
            ->route('doctor.medical-record.allergies', $allergy->patient_record_id)
            ->with('success', '✅ تم تعديل الحساسية بنجاح.');
    }

    public function allergies_Delete(Allergy $allergy)
    {
        $doctorId = auth()->user()->doctor->id;
        $patientId = $allergy->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        $allergy->delete();

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->back()->with('success', '✅ تم حذف الحساسية بنجاح.');
    }

    public function allergies_DeleteAll($patientRecordId)
    {
        $doctorId = auth()->user()->doctor->id;
        $patientRecord = Patient_record::findOrFail($patientRecordId);
        $patientId = $patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, '❌ لا توجد زيارة نشطة حالياً.');
        }

        $patientRecord->allergies()->delete();

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->back()->with('success', '✅ تم حذف كل الحساسية.');
    }

    public function allergies_Show($allergyId)
    {
        $allergy = Allergy::with('patientRecord.patient')->findOrFail($allergyId);

        $doctorId = auth()->user()->doctor->id;
        $patientId = $allergy->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, '❌ لا يمكنك عرض بيانات هذه الحساسية بدون وجود زيارة نشطة أو منتهية مع المريض.');
        }

        return view('doctor.medical-record.allergies.show', compact('allergy'));
    }


    public function medicalFiles($patientRecordId)
    {
        $doctor = Auth::user()->doctor;
        $patientRecord = Patient_record::with('patient')->findOrFail($patientRecordId);
        $patient = $patientRecord->patient;

        if (!$patientRecord) {
            return redirect()->back()->with('warning', 'لا يوجد سجل طبي لهذا المريض.');
        }

        $hasAppointment = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$hasAppointment) {
            abort(Response::HTTP_FORBIDDEN, 'ليس لديك إذن لرؤية هذا القسم.');
        }

        $files = $patientRecord->medicalFiles()->latest()->get();

        return view('doctor.appointments.patients.medicalRecord.medicalFiles.show', compact('files', 'patient'));


    }

    public function medicalFiles_Edit($medicalFileId)
    {
        $medicalFile = MedicalFile::findOrFail($medicalFileId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $medicalFile->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        return view('doctor.appointments.patients.medicalRecord.medicalFiles.edit', compact('medicalFile'));
    }

    public function medicalFiles_Update(Request $request, $medicalFileId)
    {
        $validated = $request->validate([
            'test_name' => ['required', 'string'],
            'test_laboratory' => ['required', 'string'],
            'test_date' => ['required', 'date'],
            'test_image_pdf' => [
                'nullable',
                'file',
                function ($attribute, $value, $fail) {
                    if (request()->hasFile('test_image_pdf') && is_array(request()->file('test_image_pdf'))) {
                        $fail('يسمح برفع ملف واحد فقط.');
                    }
                },
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,pptx',
                'max:10240'
            ]
        ]);

        $medicalFile = MedicalFile::findOrFail($medicalFileId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $medicalFile->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        if ($request->hasFile('test_image_pdf')) {
            $file = $request->file('test_image_pdf');
            $path = $file->store('medical_files', 'public');
            $validated['test_image_pdf'] = $path;
        }

        $validated['visit_id'] = $visit->id;
        $medicalFile->update($validated);

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()
            ->route('doctor.medical-record.medicalFiles', $medicalFile->patient_record_id)
            ->with('success', '✅ تم تعديل الملف الطبي بنجاح.');
    }


    public function medicalFiles_Create($patientId)
    {
        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي للمريض.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        return view('doctor.appointments.patients.medicalRecord.medicalFiles.create', compact('patient'));
    }

    public function medicalFiles_Store(Request $request, $patientId)
    {
        $validated = $request->validate([
            'test_name' => ['required', 'string'],
            'test_laboratory' => ['required', 'string'],
            'test_date' => ['required', 'date'],
            'test_image_pdf' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    if (request()->hasFile('test_image_pdf') && is_array(request()->file('test_image_pdf'))) {
                        $fail('يسمح برفع ملف واحد فقط.');
                    }
                },
                'mimes:jpg,jpeg,png,webp,pdf,doc,docx,xls,xlsx,pptx',
                'max:10240',
            ],
        ]);

        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::with('user')->findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي للمريض.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        // رفع الملف
        if ($request->hasFile('test_image_pdf')) {
            $filePath = $request->file('test_image_pdf')->store('uploads/medical_files', 'public');
        }

        $record->medicalFiles()->create([
            'test_name' => $validated['test_name'],
            'test_laboratory' => $validated['test_laboratory'],
            'test_date' => $validated['test_date'],
            'test_image_pdf' => $filePath ?? null,
            'visit_id' => $visit->id,
        ]);

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->route('doctor.medical-record.medicalFiles', $record->id)
            ->with('success', '✅ تم حفظ الملف الطبي بنجاح.');
    }

    public function medicalFiles_Delete($medicalFileId)
    {
        $file = MedicalFile::with('patientRecord')->findOrFail($medicalFileId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $file->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        // حذف الملف المرفوع من التخزين
        if ($file->test_image_pdf && \Storage::disk('public')->exists($file->test_image_pdf)) {
            \Storage::disk('public')->delete($file->test_image_pdf);
        }

        $file->delete();

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->back()->with('success', '🗑️ تم حذف الملف الطبي بنجاح.');
    }

    public function medicalFiles_DeleteAll($patientRecordId)
    {
        $record = Patient_record::with('medicalFiles', 'patient')->findOrFail($patientRecordId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $record->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        foreach ($record->medicalFiles as $file) {
            if ($file->test_image_pdf && \Storage::disk('public')->exists($file->test_image_pdf)) {
                \Storage::disk('public')->delete($file->test_image_pdf);
            }
            $file->delete();
        }

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->back()->with('success', '🧹 تم حذف جميع الملفات الطبية بنجاح.');
    }


    public function medicalAttachments($patientRecordId)
    {
        $doctor = auth()->user()->doctor;
        $patientRecord = Patient_record::with('patient')->findOrFail($patientRecordId);
        $patient = $patientRecord->patient;

        $hasAppointment = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$hasAppointment) {
            abort(Response::HTTP_FORBIDDEN, 'ليس لديك إذن لرؤية هذا القسم.');
        }

        $files = $patientRecord->medicalAttachment()->latest()->get();

        return view('doctor.appointments.patients.medicalRecord.medicalAttachment.index', [
            'files' => $files,
            'patient' => $patient
        ]);
    }
    public function medicalAttachments_Create($patientId)
    {
        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::findOrFail($patientId);

        $record = $patient->patient_record;
        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي لهذا المريض.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        return view('doctor.appointments.patients.medicalRecord.medicalAttachment.create', compact('patient'));
    }

    public function medicalAttachments_Store(Request $request, $patientId)
    {
        $validated = $request->validate([
            'ray_name' => 'required|string',
            'ray_laboratory' => 'required|string',
            'ray_date' => 'required|date',
            'ray_image' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240'
        ]);

        $doctorId = auth()->user()->doctor->id;
        $patient = Patient::findOrFail($patientId);
        $record = $patient->patient_record;

        if (!$record) {
            return redirect()->back()->with('error', 'لا يوجد سجل طبي.');
        }

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        if ($request->hasFile('ray_image')) {
            $path = $request->file('ray_image')->store('medical_attachments', 'public');
            $validated['ray_image'] = $path;
        }

        $validated['patient_record_id'] = $record->id;
        $validated['visit_id'] = $visit->id;

        MedicalAttachment::create($validated);

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->route('doctor.medical-record.medicalAttachments', $record->id)
            ->with('success', 'تم حفظ الملف الطبي بنجاح.');
    }

    public function medicalAttachments_Edit($medicalAttachmentId)
    {
        $medicalAttachment = MedicalAttachment::with('patientRecord.patient')->findOrFail($medicalAttachmentId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $medicalAttachment->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        return view('doctor.appointments.patients.medicalRecord.medicalAttachment.edit', compact('medicalAttachment'));
    }

    public function medicalAttachments_Update(Request $request, $medicalAttachmentId)
    {
        $validated = $request->validate([
            'ray_name' => 'required|string',
            'ray_laboratory' => 'required|string',
            'ray_date' => 'required|date',
            'ray_image' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240'
        ]);

        $medicalAttachment = MedicalAttachment::with('patientRecord')->findOrFail($medicalAttachmentId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $medicalAttachment->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        if ($request->hasFile('ray_image')) {
            if ($medicalAttachment->ray_image) {
                Storage::disk('public')->delete($medicalAttachment->ray_image);
            }
            $validated['ray_image'] = $request->file('ray_image')->store('medical_attachments', 'public');
        }

        $validated['visit_id'] = $visit->id;
        $medicalAttachment->update($validated);

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return redirect()->route('doctor.medical-record.medicalAttachments', $medicalAttachment->patient_record_id)
            ->with('success', 'تم تعديل الملف الطبي.');
    }

    public function medicalAttachments_Delete($medicalAttachmentId)
    {
        $medicalAttachment = MedicalAttachment::with('patientRecord')->findOrFail($medicalAttachmentId);
        $doctorId = auth()->user()->doctor->id;
        $patientId = $medicalAttachment->patientRecord->patient_id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        if ($medicalAttachment->ray_image) {
            Storage::disk('public')->delete($medicalAttachment->ray_image);
        }

        $medicalAttachment->delete();

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return back()->with('success', 'تم حذف الملف الطبي.');
    }

    public function medicalAttachments_DeleteAll($patientRecordId)
    {
        $patientRecord = Patient_record::findOrFail($patientRecordId);
        $patientId = $patientRecord->patient_id;
        $doctorId = auth()->user()->doctor->id;

        $visit = $this->getActiveVisitForPatientAndDoctor($patientId, $doctorId);
        if (!$visit) {
            abort(403, 'لا توجد زيارة نشطة حالياً.');
        }

        foreach ($patientRecord->medicalAttachment as $medicalAttachmentId) {
            if ($medicalAttachmentId->ray_image) {
                Storage::disk('public')->delete($medicalAttachmentId->ray_image);
            }
            $medicalAttachmentId->delete();
        }

        $this->logMedicalRecordEdit(patientId: $patientId, visitId: $visit->id);

        return back()->with('success', 'تم حذف جميع الملفات الطبية.');
    }


    public function medicalAttachment_Show($medicalAttachmentId)
    {
        $medicalAttachment = MedicalAttachment::with('patientRecord.patient')->findOrFail($medicalAttachmentId);
        $doctorId = auth()->user()->doctor->id;
        $patient = $medicalAttachment->patientRecord->patient;

        $hasAppointment = $patient->appointments()
            ->where('doctor_id', $doctorId)
            ->whereIn('status', ['confirmed', 'completed'])
            ->exists();

        if (!$hasAppointment) {
            abort(403, 'لا تملك صلاحية عرض هذا الملف.');
        }

        return view('doctor.appointments.patients.medicalRecord.medicalAttachments.show', compact('file', 'patient'));
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
        $start = Carbon::parse($data['med_start_date']);
        $now = now();

        if ($start->gt($now)) {
            // إذا كان الدواء يبدأ في المستقبل، لا يوجد كمية بعد
            return 0;
        }

        // إذا كان الدواء مؤقت وله end date
        if ($data['med_type'] === 'current' && !empty($data['med_end_date'])) {
            $end = Carbon::parse($data['med_end_date']);
        } else {
            // chronic → بدون end_date، نحسب حتى الآن فقط
            $end = $now;
        }

        $days = $start->diffInDays($end) + 1;
        $perDay = $this->getFrequencyValue($data['med_frequency']);
        $quantityPerDose = (float) $data['med_dose'];

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

        $waiting = DB::table('waiting_list')
            ->where('appointment_id', $appointment->id)
            ->where('w_status', 'in_progress')
            ->exists();

        if (!$waiting)
            return null;

        return Visit::where('appointment_id', $appointment->id)
            ->where('v_status', 'active')
            ->first();
    }


}
