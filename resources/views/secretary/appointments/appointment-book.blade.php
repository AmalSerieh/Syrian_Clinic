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
        <form method="POST" action="{{ route('secretary.appointments.bookstore') }}"
            class="bg-gray-700 bg-opacity-80 shadow-lg text-white p-6 m-8 w-full md:w-1/2 lg:w-1/3 rounded-[50px] ml-16">
            @csrf

            <!-- Doctor -->
            <input type="hidden" name="doctor_id" value="{{ $doctor_id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <input type="hidden" name="time" value="{{ $start_time }}">
            <input type="hidden" name="end_time" value="{{ $end_time }}">
            <h1 class="text-2xl font-bold mb-6 text-center flex items-center justify-center text-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="mr-3 text-blue-500">
                    <path d="M2 21a8 8 0 0 1 13.292-6" />
                    <circle cx="10" cy="8" r="5" />
                    <path d="M19 16v6" />
                    <path d="M22 19h-6" />
                </svg>
                Add Booking
            </h1>
            <!-- Select Patient -->
            <div class="mt-4">
                <label for="patient_id" class="flex mb-1 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="7" r="4" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 21h13a2 2 0 00-13 0z" />
                    </svg>
                    Select Patient :
                </label>
                <select id="patient_id" name="patient_id" required
                    class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type of Visit -->
            <div class="mt-4">
                <label for="type_visit" class="flex mb-1 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path
                            d="M3 7v10a2 2 0 002 2h3m10-12h3a2 2 0 012 2v10a2 2 0 01-2 2h-3m0 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v14m10 0H7" />
                    </svg>
                    Type of Visit
                </label>
                <select id="type_visit" name="type_visit" required
                    class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                    <option value="appointment">appointment </option>
                    <option value="review">review</option>
                </select>
            </div>

            <!-- Location Type -->
            <div class="mt-4">
                <label for="location_type" class="flex mb-1 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7h20L12 2z M2 7v13h20V7" />
                    </svg>
                    Location Type
                </label>
                <select id="location_type" name="location_type" required
                    class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-10 text-blue-500">
                    <option value="in_Home">in_Home </option>
                    <option value="on_Street"> on_Street</option>
                    <option value="in_Clinic">in_Clinic</option>

                </select>
            </div>
            <!-- Arrived Time -->
            <div class="mt-4">
                <label for="arrivved_time" class="flex mb-1 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400 mr-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2L2 7h20L12 2z M2 7v13h20V7" />
                    </svg>
                    Arrived Time
                </label>

                <input id="arrivved_time" name="arrivved_time" required :value="old('arrivved_time')" required autofocus
                    class="w-full text-sm py-1.5 px-2 border border-blue-500 rounded-xl bg-gray-800 bg-opacity-50 text-blue-500">
            </div>

            <button type="submit"
                class="mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-2xl w-full">
                Confirm Book
            </button>
        </form>

    </div>


    </div>
@endsection
