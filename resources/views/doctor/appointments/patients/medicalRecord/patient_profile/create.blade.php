{{-- resources/views/doctor/medical-records/patient_profile/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">إنشاء الملف الطبي للمريض</h2>
    </x-slot>

    <div class="py-6 px-4 max-w-4xl mx-auto">
        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('doctor.medical-record.patient_profile.store', $patient->id) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input-label for="gender" value="الجنس" />
                <select name="gender" id="gender" class="form-select">
                    <option value="male">ذكر</option>
                    <option value="female">أنثى</option>
                </select>

                <x-input-label for="date_birth" value="تاريخ الميلاد" />
                <x-text-input type="date" name="date_birth" id="date_birth" class="form-input" />

                <x-input-label for="height" value="الطول (سم)" />
                <x-text-input type="number" name="height" id="height" min="1" class="form-input" />

                <x-input-label for="weight" value="الوزن (كغ)" />
                <x-text-input type="number" name="weight" id="weight" min="1" class="form-input" />

                <x-input-label for="blood_type" value="فصيلة الدم" />
                <select name="blood_type" id="blood_type" class="form-select">
                    @foreach(['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-','Gwada-'] as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>

                <x-input-label for="smoker" value="مدخن؟" />
                <select name="smoker" id="smoker" class="form-select">
                    <option value="1">نعم</option>
                    <option value="0">لا</option>
                </select>

                <x-input-label for="alcohol" value="يستهلك كحول؟" />
                <select name="alcohol" id="alcohol" class="form-select">
                    <option value="1">نعم</option>
                    <option value="0">لا</option>
                </select>

                <x-input-label for="drug" value="يتعاطى مخدرات؟" />
                <select name="drug" id="drug" class="form-select">
                    <option value="1">نعم</option>
                    <option value="0">لا</option>
                </select>

                <x-input-label for="matital_status" value="الحالة الاجتماعية" />
                <select name="matital_status" id="matital_status" class="form-select">
                    <option value="single">أعزب</option>
                    <option value="married">متزوج</option>
                    <option value="widower">أرمل</option>
                    <option value="divorced">مطلق</option>
                </select>
            </div>

            <div class="mt-6">
                <x-primary-button>حفظ الملف الطبي</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
