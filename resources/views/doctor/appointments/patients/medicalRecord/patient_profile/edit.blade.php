<!-- resources/views/doctor/medical-records/patient_profile/edit.blade.php -->

@extends('layouts.doctor.header')
@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ✏️ تعديل الملف الطبي للمريض
        </h2>
    </x-slot>

    <div class="py-8 px-6 max-w-4xl mx-auto text-black rounded-lg shadow">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>⚠️ {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('doctor.medical-record.patient_profile.update', $patientProfile->id) }}" method="POST">
            @csrf

            <!-- الجنس -->
            <div class="mb-4">
                <label for="gender" class="block font-bold mb-2">الجنس</label>
                <select name="gender" id="gender" class="w-full rounded border-gray-300">
                    <option value="male" {{ $patientProfile->gender == 'male' ? 'selected' : '' }}>ذكر</option>
                    <option value="female" {{ $patientProfile->gender == 'female' ? 'selected' : '' }}>أنثى</option>
                </select>
            </div>

            <!-- تاريخ الميلاد -->
            <div class="mb-4">
                <label for="date_birth" class="block font-bold mb-2">تاريخ الميلاد</label>
                <input type="date" name="date_birth" id="date_birth"
                    value="{{ old('date_birth', $patientProfile->date_birth) }}"
                    class="w-full rounded border-gray-300">
            </div>

            <!-- الطول -->
            <div class="mb-4">
                <label for="height" class="block font-bold mb-2">الطول (سم)</label>
                <input type="number" name="height" id="height"
                    value="{{ old('height', $patientProfile->height) }}"
                    class="w-full rounded border-gray-300">
            </div>

            <!-- الوزن -->
            <div class="mb-4">
                <label for="weight" class="block font-bold mb-2">الوزن (كغ)</label>
                <input type="number" name="weight" id="weight"
                    value="{{ old('weight', $patientProfile->weight) }}"
                    class="w-full rounded border-gray-300">
            </div>

            <!-- فصيلة الدم -->
            <div class="mb-4">
                <label for="blood_type" class="block font-bold mb-2">فصيلة الدم</label>
                <select name="blood_type" id="blood_type" class="w-full rounded border-gray-300">
                    @php
                        $bloodTypes = ['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-', 'Gwada-'];
                    @endphp
                    @foreach ($bloodTypes as $type)
                        <option value="{{ $type }}" {{ $patientProfile->blood_type == $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- مدخن -->
            <div class="mb-4">
                <label for="smoker" class="block font-bold mb-2">هل أنت مدخن؟</label>
                <select name="smoker" id="smoker" class="w-full rounded border-gray-300">
                    <option value="1" {{ $patientProfile->smoker ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ !$patientProfile->smoker ? 'selected' : '' }}>لا</option>
                </select>
            </div>

            <!-- كحول -->
            <div class="mb-4">
                <label for="alcohol" class="block font-bold mb-2">هل تتناول الكحول؟</label>
                <select name="alcohol" id="alcohol" class="w-full rounded border-gray-300">
                    <option value="1" {{ $patientProfile->alcohol ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ !$patientProfile->alcohol ? 'selected' : '' }}>لا</option>
                </select>
            </div>

            <!-- تعاطي مخدرات -->
            <div class="mb-4">
                <label for="drug" class="block font-bold mb-2">هل تتعاطى المخدرات؟</label>
                <select name="drug" id="drug" class="w-full rounded border-gray-300">
                    <option value="1" {{ $patientProfile->drug ? 'selected' : '' }}>نعم</option>
                    <option value="0" {{ !$patientProfile->drug ? 'selected' : '' }}>لا</option>
                </select>
            </div>

            <!-- الحالة الاجتماعية -->
            <div class="mb-6">
                <label for="matital_status" class="block font-bold mb-2">الحالة الاجتماعية</label>
                <select name="matital_status" id="matital_status" class="w-full rounded border-gray-300">
                    <option value="single" {{ $patientProfile->matital_status == 'single' ? 'selected' : '' }}>أعزب</option>
                    <option value="married" {{ $patientProfile->matital_status == 'married' ? 'selected' : '' }}>متزوج</option>
                    <option value="widower" {{ $patientProfile->matital_status == 'widower' ? 'selected' : '' }}>أرمل</option>
                    <option value="divorced" {{ $patientProfile->matital_status == 'divorced' ? 'selected' : '' }}>مطلق</option>
                </select>
            </div>

            <!-- زر الحفظ -->
            <div class="flex justify-end">
                <button type="submit"
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    💾 حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
@endsection
