<?php

namespace App\Http\Controllers\Api\PatientRecord;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PatientRecord\MedicationAlarmRequest;
use App\Http\Resources\Api\PateintRecord\MedicationAlarmResource;
use App\Models\Medication;
use App\Models\MedicationAlarm;
use App\Services\Api\PateintRecord\MedicationAlarmService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MedicationAlarmController extends Controller
{
    public function __construct(protected MedicationAlarmService $service)
    {
    }
    public function index()
    {
        $alarms = $this->service->getUserAlarms();
        return MedicationAlarmResource::collection($alarms);
    }
    // app/Http/Controllers/MedicationAlarmController.php
    public function store(MedicationAlarmRequest $request)
    {
        $user = auth()->user();

        // التحقق من وجود سجل طبي للمستخدم
        if (!$user->patient->patient_record) {
            return response()->json([
                'message' => 'يجب أن يكون لديك سجل طبي قبل إضافة منبهات الأدوية',
                'errors' => ['general' => ['السجل الطبي غير موجود']]
            ], Response::HTTP_FORBIDDEN);
        }

        $patientRecord = $user->patient->patient_record;

        // التحقق من وجود أدوية في قسم الأدوية بالسجل الطبي
        if ($patientRecord->medications()->count() === 0) {
            return response()->json([
                'message' => 'قسم الأدوية فارغ. يرجى إضافة دواء أولاً',
                'errors' => ['general' => ['لا توجد أدوية في قسم الأدوية بالسجل الطبي']]
            ], Response::HTTP_FORBIDDEN);
        }


        // التحقق من أن الدواء ينتمي إلى سجل المستخدم الطبي
        $medication = $patientRecord->medications()->find($request->medication_id);

        if (!$medication) {
            return response()->json([
                'message' => 'الدواء غير موجود في سجلك الطبي',
                'errors' => ['medication_id' => ['الدواء غير مسجل في قسم الأدوية بالسجل الطبي']]
            ], Response::HTTP_NOT_FOUND);
        }
        // التحقق من عدد الأوقات
     /*    $expectedCount = (int) $medication->med_frequency_value;
        $actualCount = count($request->alarm_times);

        if ($expectedCount !== $actualCount) {
            return response()->json([
                'message' => 'عدد أوقات التنبيه لا يتطابق مع تكرار الدواء',
                'errors' => ['alarm_times' => "يجب إدخال {$expectedCount} أوقات تنبيه لهذا الدواء"]
            ], 422);
        } */

        // إنشاء المنبهات
        $alarms = $this->service->createAlarms($medication, $request->alarm_times);

        return MedicationAlarmResource::collection($alarms);
    }
}
