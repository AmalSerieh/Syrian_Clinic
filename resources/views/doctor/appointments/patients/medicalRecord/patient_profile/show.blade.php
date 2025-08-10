<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">الملف الطبي للمريض: {{ $patient->user->name }}</h2>
    </x-slot>

    <div class="py-6 px-4 mx-auto max-w-3xl bg-white shadow rounded-xl">
        @if (!$patientProfile)
            <div class="text-center text-gray-600">
                ❌ لم يتم إدخال الملف الطبي بعد.
                 <div class="mt-6">
                <a href="{{ route('doctor.medical-record.patient_profile.create', $patient->id) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    ✏️ تعديل الملف الطبي
                </a>
            </div>
            </div>
        @else
            @php
                $birthDate = \Carbon\Carbon::parse($patientProfile->date_birth);
                $age = $birthDate->age;

                $heightMeters = $patientProfile->height / 100;
                $bmi = $heightMeters > 0 ? round($patientProfile->weight / ($heightMeters * $heightMeters), 1) : null;

                $bloodTypes = [
                    'A+' => 'A+, AB+',
                    'A-' => 'A-, A+, AB-, AB+',
                    'B+' => 'B+, AB+',
                    'B-' => 'B-, B+, AB-, AB+',
                    'AB+' => 'AB+ فقط',
                    'AB-' => 'AB-, AB+',
                    'O+' => 'O+, A+, B+, AB+',
                    'O-' => 'الكل (O-, O+, A-, A+, B-, B+, AB-, AB+)',
                    'Gwada-' => 'Gwada-',
                ];
            @endphp

            <div class="space-y-4">
                <div><strong>الجنس:</strong> {{ $patientProfile->gender == 'male' ? 'ذكر' : 'أنثى' }}</div>
                <div><strong>تاريخ الميلاد:</strong> {{ $birthDate->format('Y-m-d') }} (العمر: {{ $age }} سنة)
                </div>
                <div><strong>الطول:</strong> {{ $patientProfile->height }} سم</div>
                <div><strong>الوزن:</strong> {{ $patientProfile->weight }} كغ</div>
                <div><strong>مؤشر كتلة الجسم (BMI):</strong> {{ $bmi }}</div>
                <div>
                    <strong>فصيلة الدم:</strong> {{ $patientProfile->blood_type }}
                    <br>
                    <strong>يمكنه استقبال من:</strong> {{ implode(', ', $patientProfile->getAcceptedBloodTypes()) }}

                </div>
                <div><strong>هل يدخن؟</strong> {{ $patientProfile->smoker ? 'نعم' : 'لا' }}</div>
                <div><strong>هل يشرب الكحول؟</strong> {{ $patientProfile->alcohol ? 'نعم' : 'لا' }}</div>
                <div><strong>هل يتعاطى مخدرات؟</strong> {{ $patientProfile->drug ? 'نعم' : 'لا' }}</div>
                <div><strong>الحالة الاجتماعية:</strong>
                    {{ $patientProfile->matital_status }}

                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('doctor.medical-record.patient_profile.edit', $patientProfile->id) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                    ✏️ تعديل الملف الطبي
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
