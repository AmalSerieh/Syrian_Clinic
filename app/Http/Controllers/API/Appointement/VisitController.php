<?php

namespace App\Http\Controllers\API\Appointement;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecordLogVisit;
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
                    'created_at' =>  $prescription->created_at->format('Y-m-d'),
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


}
