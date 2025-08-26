<?php

namespace App\Http\Controllers\API\Appointement;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecordLogVisit;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Visit;
use App\Models\VisitEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    //هنا بدي يتم جلب آخر زيارة حديثة تم التعديل على السجل فيها بحيث بيتم جلب الاسم الطبيب
    public function latestEditedVisit(Request $request)
    {
        // المريض الحالي المسجل دخول
        $patientId = $request->user()->patient->id;
        // جلب آخر سجل تم التعديل عليه للمريض
        $latestLog = MedicalRecordLogVisit::with('doctor')
            ->where('patient_id', $patientId)
            ->latest('edited_at')
            ->first();

        if (!$latestLog) {
            return response()->json([
                'status' => false,
                'message' => 'لا يوجد زيارات معدلة لهذا المريض'
            ], 404);
        }

        // تنسيق التاريخ باستخدام Carbon
        $date = $latestLog->edited_at;
        $dayName = $date->translatedFormat('l');   // اسم اليوم (مثلاً: الاثنين)
        $monthName = $date->translatedFormat('F');   // اسم الشهر (مثلاً: أغسطس)
        $year = $date->year;
        // تحديد اللغة من الـ header (مثلاً Accept-Language: ar أو en)
        $locale = $request->header('Accept-Language', 'ar'); // الافتراضي عربي
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar'; // fallback
        }

        $specialist = $locale === 'en'
            ? $latestLog->doctor?->doctorProfile?->specialist_en : $latestLog->doctor?->doctorProfile?->specialist_ar;

        return response()->json([
            'status' => true,
            'visit_id' => $latestLog->visit_id,
            'doctor_name' => $latestLog->doctor?->user->name,
            'doctor_specialist' => $specialist,
            'edited_at' => $date,
            'day_name' => $dayName,
            'month_name' => $monthName,
            'year' => $year,
        ]);
    }

    //هلأ بدي آخر زيارة قام بها  هذا المريض حيث الزيارة منهية و الموعد مكتمل
    //رح اعمل التقييم
    public function storeEvaluation(Request $request, $visitId)
    {
        // التحقق من أن المستخدم هو مريض
        if (!auth()->user()->patient) {
            return response()->json([
                'status' => false,
                'message' => 'المستخدم الحالي ليس مريضاً'
            ], 403);
        }
        $request->validate([
            'treatment_final' => 'required|integer|min:0|max:100',
            'handling' => 'required|integer|min:0|max:100',
            'services' => 'required|integer|min:0|max:100',
            'final_evaluate' => 'required|integer|min:0|max:100',

        ]);

        $visit = Visit::with('doctor')
            ->where('id', $visitId)
            ->where('v_status', 'completed') // فقط الزيارات المكتملة
            ->firstOrFail();

        if (!$visit) {
            return response()->json([
                'status' => false,
                'message' => 'الزيارة غير موجودة أو لم تكتمل بعد'
            ], 404);
        }
        // التحقق من أن المريض هو من قام بالزيارة
        if ($visit->appointment->patient_id !== auth()->user()->patient->id) {
            return response()->json([
                'status' => false,
                'message' => 'غير مصرح لك بتقييم هذه الزيارة'
            ], 403);
        }
        // التحقق من عدم وجود تقييم سابق لنفس المريض والزيارة
        $existingEvaluation = VisitEvaluation::where('visit_id', $visitId)
            ->where('patient_id', auth()->user()->patient->id)
            ->first();


        if ($existingEvaluation) {
            return response()->json([
                'status' => false,
                'message' => 'لقد قمت بتقييم هذه الزيارة مسبقاً'
            ], 422);
        }

        $evaluation = VisitEvaluation::updateOrCreate(
            [
                'visit_id' => $visit->id,
                'patient_id' => $request->user()->patient->id,
                'doctor_id' => $visit->doctor_id,
            ],
            [
                'treatment_final' => $request->treatment_final,
                'handling' => $request->handling,
                'services' => $request->services,
                'final_evaluate' => $request->final_evaluate,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'تم إدخال التقييم بنجاح',
            'data' => $evaluation,
            'doctor_name' => $visit->doctor->user->name
        ]);
    }
    //و هلأ بدي الوصفة ترجع بحيث بيتم إرحاع كل وصفة و كل زيارة على حدا

    public function getPrescription()
    {
        // التحقق من أن المستخدم مريض
        if (!Auth::check() || !Auth::user()->patient) {
            return response()->json([
                'status' => false,
                'message' => 'يجب تسجيل الدخول كمريض أولاً'
            ], 401);
        }

        $patientId = Auth::user()->patient->id;

        // جلب جميع الوصفات الخاصة بالمريض مع العلاقات مرتبة من الأحدث
        $prescriptions = Prescription::with([
            'items',
            'visits' => function ($query) {
                $query->with(['appointment']);
            },
            'doctor.user'
        ])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get();

        // إذا لم توجد وصفات
        if ($prescriptions->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'لا توجد وصفات طبية لهذا المريض',
                'data' => []
            ]);
        }

        // تجميع الوصفات حسب الزيارة
        $groupedPrescriptions = $prescriptions->groupBy('visit_id');

        $result = [];

        foreach ($groupedPrescriptions as $visitId => $visitPrescriptions) {
            $visit = $visitPrescriptions->first()->visit;
            // تعديل هنا: تحويل التاريخ إلى تنسيق Y-m-d فقط
            $visitDate = $visit->v_started_at ?? $visitPrescriptions->first()->created_at;
            $formattedDate = $visitDate instanceof \Carbon\Carbon
                ? $visitDate->format('Y-m-d')
                : date('Y-m-d', strtotime($visitDate));

            $visitData = [
                'visit_id' => $visitId,
                'visit_date' => $formattedDate, // التاريخ فقط بدون وقت
                'doctor_name' => $visitPrescriptions->first()->doctor->user->name ?? 'غير معروف',
                'appointment_date' => $visit->appointment->date ?? null,
                'prescriptions' => []
            ];

            foreach ($visitPrescriptions as $prescription) {
                $visitData['prescriptions'][] = [
                    'prescription_id' => $prescription->id,
                    'created_at' => $prescription->created_at->format('Y-m-d'),
                    'items' => $prescription->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->pre_name,
                            'scientific_name' => $item->pre_scientific,
                            'type' => $item->pre_type,
                            'dosage_form' => $item->pre_dosage_form,
                            'dose' => $item->pre_dose,
                            'frequency' => $item->pre_frequency,
                            'timing' => $item->pre_timing,
                            'start_date' => $item->pre_start_date,
                            'end_date' => $item->pre_end_date,
                            'quantity' => $item->pre_total_quantity,
                            'taken_quantity' => $item->pre_taken_quantity,
                            'alternatives' => $item->getAlternativesList(),
                            'instructions' => $item->instructions,
                            'prescribed_by' => $item->pre_prescribed_by_doctor
                        ];
                    })
                ];
            }

            $result[] = $visitData;
        }

        // ترتيب النتائج من الأحدث إلى الأقدم حسب تاريخ الزيارة
        usort($result, function ($a, $b) {
            return strtotime($b['visit_date']) - strtotime($a['visit_date']);
        });

        return response()->json([
            'status' => true,
            'message' => 'تم جلب الوصفات بنجاح',
            'data' => $result
        ]);
    }
    public function getPrescription1()
    {
        $patientId = Auth::user()->patient->id;

        $prescriptions = Prescription::with([
            'items' => function ($query) {
                $query->orderBy('pre_type')
                    ->orderBy('pre_start_date', 'desc');
            },
            'visits.appointment',
            'doctor.user'
        ])
            ->where('patient_id', $patientId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('visit_id');

        return $prescriptions;
    }


    public function AllVisit(Request $request, $patientId = null)
    {
        $user = $request->user();

        // استخدام patientId من المستخدم إذا لم يُحدد
        if (!$patientId && $user->patient) {
            $patientId = $user->patient->id;
        }

        if (!$patientId) {
            return response()->json([
                'message' => 'لم يتم تحديد المريض',
                'data' => []
            ], 400);
        }

        $patient = Patient::find($patientId);
        if (!$patient) {
            return response()->json([
                'message' => 'المريض غير موجود',
                'data' => []
            ], 404);
        }

        // جلب المواعيد المكتملة
        $completedAppointments = Appointment::with('doctor')
            ->where('patient_id', $patientId)
            ->where('status', 'completed')
            ->orderBy('date', 'desc')
            ->get();

        // جلب الزيارات المنتهية
        $completedVisits = Visit::with('doctor')
            ->where('patient_id', $patientId)
            ->where('v_status', 'completed')
            ->orderBy('v_ended_at', 'desc')
            ->get();

        // تحديد اللغة من الـ header (مثلاً Accept-Language: ar أو en)
        $locale = $request->header('Accept-Language', 'ar');
        if (!in_array($locale, ['ar', 'en'])) {
            $locale = 'ar';
        }



        // دمج المواعيد والزيارات مع اختيار التخصص حسب اللغة لكل طبيب
        $allCompleted = $completedAppointments->map(function ($appointment) use ($locale) {
            $specialist = $locale === 'en'
                ? $appointment->doctor->doctorProfile?->specialist_en
                : $appointment->doctor->doctorProfile?->specialist_ar;

            return [
                'type' => 'appointment',
               // 'id' => $appointment->id,
                'date' => $appointment->date,
                'time' => $appointment->start_time .'-'.$appointment->end_time,
                'status' => $appointment->status,
                'doctor' => [
                    'id' => $appointment->doctor->id,
                    'name' => $appointment->doctor->user->name,
                    'specialization' => $specialist,
                ]
            ];
        })->merge($completedVisits->map(function ($visit) use ($locale) {
            $specialist = $locale === 'en'
                ? $visit->doctor->doctorProfile?->specialist_en
                : $visit->doctor->doctorProfile?->specialist_ar;

            return [
                'type' => 'visit',
                'id' => $visit->id,
                'date' => $visit->v_ended_at,
                'status' => $visit->v_status,
                'doctor' => [
                    'id' => $visit->doctor->id,
                    'name' => $visit->doctor->user->name,
                    'specialization' => $specialist,
                ]
            ];
        }));

        if ($allCompleted->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد زيارات مكتملة لهذا المريض',
                'data' => []
            ], 200);
        }

        return response()->json([
            'message' => 'تم جلب كل الزيارات المكتملة',
            'data' => $allCompleted
        ], 200);
    }

}
