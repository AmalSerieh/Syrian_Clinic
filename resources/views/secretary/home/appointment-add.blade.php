@extends('layouts.secretary.header')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @if ($errors->any())
        <div class="bg-red-500 text-white p-2 rounded mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="h-screen flex items-center justify-start"
        style="background-image: url('{{ asset('images/admin/secretary/secretary.png') }}'); background-size: cover; background-position: center;">
        <!-- فورم إضافة الطبيب -->
        {{--  <div class="bg-gray-700 bg-opacity-70 shadow-lg text-white p-6 m-8 w-full md:w-1/2 lg:w-1/3"
                style="border-radius: 50px; margin-left: 60px;"> --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- اختيار المريض -->
            <div class="col-span-1">
                <label for="patient_id" class="block mb-2 text-sm font-medium text-gray-700">المريض</label>
                <select id="patient_id" name="patient_id" class="w-full p-2 border rounded-lg">
                    <option value="">اختر المريض</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- اختيار الطبيب -->
            <div class="col-span-1">
                <label for="doctor_id" class="block mb-2 text-sm font-medium text-gray-700">الطبيب</label>
                <select id="doctor_id" name="doctor_id" class="w-full p-2 border rounded-lg">
                    <option value="">اختر الطبيب</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- اختيار الموعد -->
            <div class="col-span-1">
                <label for="appointment_slot" class="block mb-2 text-sm font-medium text-gray-700">الموعد</label>
                <select id="appointment_slot" name="appointment_slot" class="w-full p-2 border rounded-lg" disabled>
                    <option value="">اختر الطبيب أولاً</option>
                </select>
            </div>
        </div>

    </div>


    </div>
@endsection
