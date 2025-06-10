<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Patient_record;
use Illuminate\Http\Request;

class PatientRecordController extends Controller
{
    // ⛔ عرض السجل الحالي للمريض أو الطبيب
    public function show($id)
    {
        $record = Patient_record::findOrFail($id);
        $this->authorize('view', $record);

        return response()->json([
            'record' => $record->load([
                'diseases',
                'medications',
                'operations',
                'tests',
                'allergies',
                'familyHistories',
                'medicalFiles',
                'patient_profile'
            ])
        ]);
    }

    // ✅ إنشاء سجل للمريض (مرة واحدة فقط)
    public function store(Request $request)
    {
        // تحقق من أن المريض لديه سجل طبي مسبق
        if (Patient_record::where('patient_id', $request->patient_id)->exists()) {
            return response()->json(['message' => 'المريض لديه سجل طبي مسبق.'], 403);
        }

        // تأكد من أن المريض يمكنه إنشاء السجل الطبي فقط إذا لم يكن قد أنشأه مسبقًا
        $this->authorize('create', Patient_record::class);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id|unique:patient_records,patient_id',
            // أي بيانات إضافية للسجل يمكن إضافتها هنا
        ]);

        $record = Patient_record::create($validated);
        // إنشاء التفرعات الثمانية فارغة
        $record->diseases()->createMany([]);
        $record->medications()->createMany([]);
        $record->operations()->createMany([]);
        $record->tests()->createMany([]);
        $record->allergies()->createMany([]);
        $record->familyHistories()->createMany([]);
        $record->medicalFiles()->createMany([]);
        $record->patient_profile()->create();  // القسم الأول الفارغ


        return response()->json([
            'message' => 'تم إنشاء السجل الطبي بنجاح.',
            'record' => $record->load([
                'diseases',
                'medications',
                'operations',
                'tests',
                'allergies',
                'familyHistories',
                'medicalFiles',
                'patient_profile'
            ])
        ], 201);
    }

    // 📝 تعديل السجل (الطبيب فقط)
    public function update(Request $request, $id)
    {
        $record = Patient_record::findOrFail($id);
        $this->authorize('update', $record);

        $validated = $request->validate([
            // ضف الحقول القابلة للتعديل هنا حسب تصميمك
        ]);

        $record->update($validated);

        return response()->json([
            'message' => 'تم تحديث السجل الطبي بنجاح.',
            'record' => $record
        ]);
    }

    // ❌ حذف السجل (اختياري - الطبيب فقط)
    public function destroy($id)
    {
        $record = Patient_record::findOrFail($id);
        $this->authorize('delete', $record);

        $record->delete();

        return response()->json([
            'message' => 'تم حذف السجل الطبي بنجاح.'
        ]);
    }
}
