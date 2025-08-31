@extends('layouts.doctor.header')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @if (session('error'))
        <div class="mb-6 rounded bg-red-100 border border-red-300 text-red-800 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif
    @if (session('status'))
        <div class="bg-green-500 text-white p-2 rounded mb-3 ">
            {{ session('status') }}
        </div>
    @endif

    {{-- Main Content --}}
    <div class="p-4 grid grid-cols-12 gap-6 -mt-5">

        {{-- Left Section --}}
        <div class="col-span-8 space-y-5">
            {{-- Today's statistics --}}
            <div class="bg-[#062E47] p-6 rounded-xl text-white">
                <h2 class="text-lg font-semibold">Today's statistics</h2>
                <h6 class="text-gray-500 text-sm mb-4">Sales summary</h6>

                <!-- Appointment Stats Cards -->
                <div class="grid grid-cols-3 gap-4 -mt-2 text-white">
                    <!-- Done -->
                    <div class="bg-green-900/30 border border-green-700 p-5 rounded-xl shadow flex items-center gap-3">
                        <div class="bg-green-700 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-green-300 text-sm">Total Done Date</h2>
                            <p class="text-xl font-bold text-green-200">14 Dates</p>
                        </div>
                    </div>

                    <!-- Canceled -->
                    <div class="bg-red-900/30 border border-red-700 p-5 rounded-xl shadow flex items-center gap-3">
                        <div class="bg-red-700 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-red-300 text-sm">Total Cancel Date</h2>
                            <p class="text-xl font-bold text-red-200">5 Dates</p>
                        </div>
                    </div>

                    <!-- All Dates -->
                    <div class="bg-yellow-900/30 border border-yellow-700 p-5 rounded-xl shadow flex items-center gap-3">
                        <div class="bg-yellow-700 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-yellow-300 text-sm">Total Dates</h2>
                            <p class="text-xl font-bold text-yellow-200">19 Dates</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Patient Info Card --}}
            @if ($currentPatient)
                <div class="bg-[#062E47] p-4 rounded-xl text-white">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold">
                            Patient Info: {{ $currentPatient['patient']['user']['name'] }}
                        </h3>
                        <a href="{{ route('doctor.patients.medicalRecord.show', $currentPatient['patient']['id']) }}"
                            class="bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded-md text-white text-sm">
                            عرض السجل الطبي
                        </a>
                    </div>


                    <div class="grid grid-cols-2 gap-4">
                        {{-- Box 1 --}}
                        <div class="bg-gray-900 rounded-2xl p-4">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-white font-semibold">Public</h2>
                                <button class="p-1 rounded-md border border-blue-400 bg-blue-400/20 hover:bg-blue-400/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-black" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 5v14M5 12h14" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Basic Data --}}
                            <div class="grid grid-cols-4 gap-4 text-center">

                                {{-- Weight --}}
                                <div>
                                    <p class="text-gray-400 text-xs flex items-center justify-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path
                                                d="M5 20h14v-2H5v2zm7-18C8.134 2 5 5.134 5 9c0 2.36 1.235 4.444 3.084 5.662l-1.51 5.69h10.852l-1.51-5.69A6.978 6.978 0 0 0 19 9c0-3.866-3.134-7-7-7z" />
                                        </svg>
                                        Weight :
                                    </p>
                                    <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">
                                        {{ $currentPatient['patient']['weight'] ?? 'N/A' }} </p>
                                </div>

                                {{-- Height --}}
                                <div>
                                    <p class="text-gray-400 text-xs flex items-center justify-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M12 2v20m6-4h-4m-4 0H6" />
                                        </svg>
                                        Height :
                                    </p>
                                    <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">
                                        {{ $currentPatient['patient']['height'] ?? 'N/A' }} </p>
                                </div>

                                {{-- Gender --}}
                                <div>
                                    <p class="text-gray-400 text-xs flex items-center justify-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <circle cx="12" cy="7" r="4" />
                                            <path d="M5.5 21h13" />
                                        </svg>
                                        Gender :
                                    </p>
                                    <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">
                                        {{ $currentPatient['patient']['gender'] ?? 'N/A' }}</p>
                                </div>

                                {{-- Blood Group --}}
                                <div>
                                    <p class="text-gray-400 text-xs flex items-center justify-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M12 21a9 9 0 0 0 9-9c0-4.97-9-13-9-13S3 7.03 3 12a9 9 0 0 0 9 9z" />
                                        </svg>
                                        Blood :
                                    </p>
                                    <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">
                                        {{ $currentPatient['patient']['blood_group'] ?? 'N/A' }}</p>
                                </div>
                            </div>

                            {{-- Addictions --}}
                            {{-- Addictions --}}
                            <div class="mt-4">
                                <h2 class="text-gray-400 font-semibold mb-2">Addictions :</h2>
                                <div class="flex flex-wrap gap-2 justify-center">

                                    {{-- Smoking --}}
                                    <div class="flex items-center gap-2 bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">
                                        <span>Smoking</span>
                                        <span
                                            class="w-3 h-3 rounded-full
                {{ $currentPatient['patient']['smoker'] ? 'bg-green-500' : 'bg-red-500' }}">
                                        </span>
                                    </div>

                                    {{-- Alcohol --}}
                                    <div class="flex items-center gap-2 bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">
                                        <span>Alcohol</span>
                                        <span
                                            class="w-3 h-3 rounded-full
                {{ $currentPatient['patient']['alcohol'] ? 'bg-green-500' : 'bg-red-500' }}">
                                        </span>
                                    </div>

                                    {{-- Drugs --}}
                                    <div class="flex items-center gap-2 bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">
                                        <span>Drugs</span>
                                        <span
                                            class="w-3 h-3 rounded-full
                {{ $currentPatient['patient']['drugs'] ? 'bg-green-500' : 'bg-red-500' }}">
                                        </span>
                                    </div>

                                </div>
                            </div>

                        </div>


                        {{-- Box 2 --}}
                        {{-- Box 2 --}}
                        <div class="bg-gray-900 p-4 rounded-xl h-60 flex flex-col"> {{-- كارد بارتفاع ثابت --}}
                            <div class="flex justify-between items-center mb-4 flex-none">
                                <h3 class="text-white font-semibold">Sensitivity</h3>
                                <button class="p-1 rounded-md border border-blue-400 bg-blue-400/20 hover:bg-blue-400/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-black" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 5v14M5 12h14" />
                                    </svg>
                                </button>
                            </div>

                            @if (
                                $currentPatient &&
                                    isset($currentPatient['patient']['allergies']) &&
                                    count($currentPatient['patient']['allergies']) > 0)
                                @php
                                    $allergies = collect($currentPatient['patient']['allergies']);
                                @endphp

                                {{-- كل الحساسية داخل Scroll --}}
                                <div class="flex-1 overflow-y-auto scrollbar-hide space-y-3 pr-2">
                                    @foreach ($allergies as $allergy)
                                        <div class="bg-[#11283f] p-3 rounded-md text-blue-300">
                                            <p><span class="font-semibold text-white">Name:</span>
                                                {{ $allergy['aller_name'] }}</p>
                                            <p><span class="font-semibold text-white">Type:</span>
                                                {{ $allergy['aller_type'] }}</p>
                                            <p><span class="font-semibold text-white">Cause:</span>
                                                {{ $allergy['aller_cause'] }}</p>
                                            <p><span class="font-semibold text-white">Treatment:</span>
                                                {{ $allergy['aller_treatment'] }}</p>
                                            <p><span class="font-semibold text-white">Prevention:</span>
                                                {{ $allergy['aller_pervention'] }}</p>
                                            <p><span class="font-semibold text-white">Reasons:</span>
                                                {{ $allergy['aller_reasons'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-400 text-sm">No sensitivity data</p>
                            @endif
                        </div>




                        {{-- Box 4 - Diseases --}}
                        <div class="bg-gray-900 p-4 rounded-lg h-64 flex flex-col r">
                            <div class="flex justify-between items-center mb-4 flex-none">
                                <h3 class="text-white font-semibold">Diseases</h3>

                            </div>

                            @if (
                                $currentPatient &&
                                    isset($currentPatient['patient']['diseases']) &&
                                    count($currentPatient['patient']['diseases']) > 0)
                                @php
                                    $diseases = collect($currentPatient['patient']['diseases']);
                                @endphp

                                {{-- كل الأمراض داخل Scroll --}}
                                <div class="flex-1 overflow-y-auto scrollbar-hide space-y-3 pr-2">
                                    @foreach ($diseases as $disease)
                                        <div class="bg-[#11283f] p-3 rounded-md text-green-300">
                                            <p><span class="font-semibold text-white">Name:</span>
                                                {{ $disease['d_name'] }}</p>
                                            <p><span class="font-semibold text-white">Diagnosis Date:</span>
                                                {{ $disease['d_diagnosis_date'] }}</p>
                                            <p><span class="font-semibold text-white">Doctor:</span>
                                                {{ $disease['d_doctor'] }}</p>
                                            <p><span class="font-semibold text-white">Advice:</span>
                                                {{ $disease['d_advice'] }}</p>
                                            <p><span class="font-semibold text-white">Prohibitions:</span>
                                                {{ $disease['d_prohibitions'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-400 text-sm">No disease data</p>
                            @endif
                        </div>


                        {{-- Box 4 --}}
                        {{-- Box 5 - Medications --}}
                        <div class="bg-gray-900 p-4 rounded-lg h-64 flex flex-col"> {{-- ارتفاع ثابت للكارد --}}
                            <div class="flex justify-between items-center mb-4 flex-none">
                                <h3 class="text-white font-semibold">Medications</h3>

                            </div>

                            @if (
                                $currentPatient &&
                                    isset($currentPatient['patient']['medications']) &&
                                    count($currentPatient['patient']['medications']) > 0)
                                @php
                                    $medications = collect($currentPatient['patient']['medications']);
                                @endphp

                                {{-- كل الأدوية داخل Scroll --}}
                                <div class="flex-1 overflow-y-auto scrollbar-hide space-y-3 pr-2">
                                    @foreach ($medications as $medication)
                                        <div class="bg-[#11283f] p-3 rounded-md text-purple-300">
                                            <p><span class="font-semibold text-white">Type:</span>
                                                {{ $medication['med_type'] }}</p>
                                            <p><span class="font-semibold text-white">Name:</span>
                                                {{ $medication['med_name'] }}</p>
                                            <p><span class="font-semibold text-white">Start Date:</span>
                                                {{ $medication['med_start_date'] }}</p>
                                            <p><span class="font-semibold text-white">Frequency:</span>
                                                {{ $medication['med_frequency'] }}</p>
                                            <p><span class="font-semibold text-white">Dose:</span>
                                                {{ $medication['med_dose'] }}</p>
                                            <p><span class="font-semibold text-white">Timing:</span>
                                                {{ $medication['med_timing'] }}</p>
                                            <p><span class="font-semibold text-white">Prescribed By:</span>
                                                {{ $medication['med_prescribed_by_doctor'] }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-400 text-sm flex-1 flex items-center justify-center">No medication data
                                </p>
                            @endif
                        </div>

                    </div>
                </div>
            @else
                <div class="bg-[#062E47] p-4 rounded-xl text-white">
                    <h3 class="font-semibold mb-3">
                        Patient Info:
                    </h3>



                    <div class="grid grid-cols-2 gap-4">
                        {{-- Box 1 --}}
                        <div class="bg-gray-900  p-4 rounded-lg min-h-[200px] flex flex-col ">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-white font-semibold">Public</h2>
                                <button class="p-1 rounded-md border border-blue-400 bg-blue-400/20 hover:bg-blue-400/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-black" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 5v14M5 12h14" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Basic Data --}}
                            <div class="grid grid-cols-4 gap-4 ">
                                <p class="text-gray-400 text-sm">No data yet</p>
                            </div>


                        </div>

                        {{-- Box 2 --}}
                        <div class="bg-gray-900 p-4 rounded-lg min-h-[200px] flex flex-col ">
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-white font-semibold">Public</h2>
                                <button class="p-1 rounded-md border border-blue-400 bg-blue-400/20 hover:bg-blue-400/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-black" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 5v14M5 12h14" />
                                    </svg>
                                </button>
                                <h3 class="font-semibold mb-2">Sensitivity</h3>

                            </div>
                            <p class="text-gray-400 text-sm">No sensitivity data</p>
                        </div>

                        {{-- Box 3 --}}
                        <div class="bg-gray-900 p-4 rounded-lg min-h-[200px] flex flex-col ">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-semibold mb-2">Sensitivity</h3>

                            </div>
                            <p class="text-gray-400 text-sm">No sensitivity data</p>
                        </div>

                        {{-- Box 4 --}}
                        <div class="bg-gray-900 p-4 rounded-lg min-h-[200px] flex flex-col ">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="font-semibold mb-2">Sensitivity</h3>

                            </div>
                            <p class="text-gray-400 text-sm">No sensitivity data</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Bottom Buttons + Case --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-4 h-full">
                    <form action="{{ route('doctor.cancelToday') }}" method="POST"
                        onsubmit="return confirm('هل أنت متأكد من إلغاء جميع المواعيد اليوم؟');">
                        @csrf
                        <button type="submit"
                            class="flex-1 border-2 border-dashed border-red-500 text-red-500 rounded-xl hover:bg-red-500/20 hover:text-white transition text-lg font-semibold">
                            Cancel Today
                        </button>
                    </form>
                    <!-- زر فتح المودال -->
                    <button id="openPostponeModalBtn"
                        onclick="document.getElementById('postponeModal').classList.remove('hidden')"
                        class="flex-1 border-2 border-dashed border-yellow-500 text-yellow-500 rounded-xl hover:bg-yellow-500/20 hover:text-white transition text-lg font-semibold">
                        Postponement تأجيل المواعيد
                    </button>

                    <!-- المودال -->
                    <div id="postponeModal"
                        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                        <div class="bg-blue-400/50 p-6 rounded-xl shadow-lg w-96 text-black">
                            <h2 class="text-lg font-semibold mb-4">تأجيل جميع مواعيد اليوم</h2>

                            <form action="{{ route('doctor.appointments.postpone') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label for="postponeMinutes" class="block text-sm font-medium">
                                        عدد الدقائق للتأجيل:
                                    </label>
                                    <input type="number" id="postponeMinutes" name="minutes"
                                        class="mt-1 block w-full border rounded-lg p-2" required min="1">
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button type="button"
                                        onclick="document.getElementById('postponeModal').classList.add('hidden')"
                                        class="px-4 py-2 bg-gray-300 rounded-lg">
                                        إلغاء
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded-lg">
                                        تأكيد
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>



                    <!-- الزر لفتح المودال -->
                    <div x-data="{ open: false }">
                        <!-- الزر لفتح المودال -->
                        <button @click="open = true"
                            class="flex-1 border-2 border-dashed border-blue-500 text-blue-500 rounded-xl hover:bg-blue-500/20 hover:text-white transition text-lg font-semibold">
                            New nurse
                        </button>

                        <!-- المودال -->
                        <div x-show="open" x-cloak
                            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                            <div class="bg-gray-700 rounded-xl p-6 w-full max-w-lg relative text-white">
                                <button @click="open = false"
                                    class="absolute top-2 right-2 text-red-500 font-bold">✖</button>
                                <h2 class="text-2xl font-bold mb-4 text-center text-blue-400">إضافة ممرضة جديدة</h2>

                                <form action="{{ route('doctor.nurse.nurseStor') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <!-- حقول الإدخال -->
                                    <input type="text" name="name" placeholder="اسم الممرضة" required
                                        class="w-full mb-3 px-3 py-2 rounded text-black">
                                    <input type="email" name="email" placeholder="البريد الإلكتروني" required
                                        class="w-full mb-3 px-3 py-2 rounded text-black">
                                    <input type="text" name="phone" placeholder="رقم الهاتف" required
                                        class="w-full mb-3 px-3 py-2 rounded text-black">
                                    <input type="date" name="date_of_appointment" required
                                        class="w-full mb-3 px-3 py-2 rounded text-black">
                                    <select name="gender" required class="w-full mb-3 px-3 py-2 rounded text-black">
                                        <option value="female">أنثى</option>
                                        <option value="male">ذكر</option>
                                    </select>

                                    <div class="mb-3 max-h-48 overflow-y-auto border p-2 rounded bg-gray-800">
                                        @foreach ($services as $service)
                                            <label class="flex items-center gap-2">
                                                <input type="checkbox" name="services[]" value="{{ $service->id }}">
                                                {{ $service->serv_name }} - ({{ $service->serv_price }}$)
                                            </label>
                                        @endforeach
                                    </div>

                                    <input type="file" name="photo" accept="image/*"
                                        class="w-full mb-3 text-black">

                                    <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-600 w-full py-2 rounded font-bold mt-3">إضافة
                                        الممرضة</button>
                                </form>
                            </div>
                        </div>
                    </div>


                </div>
                <div>
                    <p class="text-lg">case description</p>
                    <textarea class="bg-transparent border border-blue-500 text-white rounded-xl p-2 w-full min-h-[160px]"></textarea>
                </div>
            </div>
        </div>

        {{-- Right Section --}}
        <div class="col-span-4 space-y-6">
            <div class="p-4 space-y-6 text-white bg-[#062E47] rounded-lg">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-semibold">Patients in the clinic</h2>
                    </div>

                    <ul class="space-y-3 max-h-32 overflow-y-auto scrollbar-hide">
                        @forelse ($waitingPatients as $index => $appointment)
                            @php
                                $waitingTime = $appointment['waiting_list']['w_check_in_time'] ?? null;
                                $statusLabel = $index === 0 ? 'Next' : 'Waiting';
                                $statusColor = $index === 0 ? 'green' : 'yellow';
                                $isNext = $index === 0;
                            @endphp

                            <li class="flex items-center justify-between bg-[#0e1b26] p-1 rounded-md">
                                <div class="flex items-center space-x-3">
                                    <img src="{{ asset('storage/' . $appointment['patient']['photo']) }}" alt="Patient"
                                        class="w-10 h-10 rounded-full object-cover" />
                                    <div>
                                        <p class="font-semibold">{{ $appointment['patient']['user']['name'] }}</p>
                                        <p class="text-sm text-gray-400">{{ $appointment['doctor']['user']['name'] }} -
                                            Appointment</p>
                                        @if ($waitingTime)
                                            <p class="text-xs text-gray-400">Arrived at:
                                                {{ \Carbon\Carbon::parse($waitingTime)->format('H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if ($isNext)
                                    <form method="POST"
                                        action="{{ route('doctor.appointments.enterConsultation', $appointment['id']) }}">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1 text-xs rounded-full bg-green-600/35 text-green-600 hover:bg-green-600 hover:text-white">
                                            Enter
                                        </button>
                                    </form>
                                @else
                                    <span class="px-3 py-1 text-xs rounded-full bg-yellow-600/35 text-yellow-600">
                                        Waiting
                                    </span>
                                @endif

                            </li>
                        @empty
                            <!-- إذا ما في مرضى -->
                            <div class="text-center p-4 text-gray-400">
                                No patients yet.
                                <form method="POST" action="{{ route('doctor.appointments.enterConsultation', 0) }}">
                                    @csrf
                                    <button type="submit"
                                        class="mt-2 px-4 py-1 text-xs rounded-full bg-green-600/35 text-green-600 hover:bg-green-600 hover:text-white">
                                        Enter
                                    </button>
                                </form>
                            </div>
                        @endforelse
                </div>

                {{-- dd( {{$currentPatient['patient']['visiit']['id']}}) --}}
            </div>
            {{-- Medical prescription --}}
            <div class="bg-[#062E47] p-4 rounded-md">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-semibold text-lg">Medical prescription</h2>

                    <!-- زر الإضافة -->
                    {{--  <a href="{{ route('doctor.prescription.create', [
                        'patient_id' => $currentPatient['patient']['id'],
                        'appointment_id' => $currentPatient['id'],
                        'visit_id' => $currentPatient['visit']['id'],
                    ]) }}"
                        class="bg-blue-500/20 border-2 border-blue-500 border-opacity-50 p-2 rounded inline-flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-white" width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </a> --}}
                    @if ($currentPatient)
                        <form id="createPrescriptionForm" method="POST"
                            action="{{ route('doctor.prescription.create') }}">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $currentPatient['patient']['id'] }}">
                            <input type="hidden" name="appointment_id" value="{{ $currentPatient['id'] }}">
                            <input type="hidden" name="visit_id" value="{{ $currentPatient['visit']['id'] }}">

                            <button type="submit"
                                class="bg-blue-500/20 border-2 border-blue-500 border-opacity-50 p-2 rounded inline-flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="text-white" width="18" height="18"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <p class="text-muted">لا يوجد مريض عند الطبيب حالياً</p>
                    @endif





                </div>

                <!-- Single medication card -->
                <div class="max-h-56 overflow-y-auto pr-2 scrollbar-hide">
                    @if (isset($prescriptions) && count($prescriptions) > 0)
                        @foreach ($prescriptions as $prescription)
                            <div class="mb-6">
                                {{--  <h3 class="text-lg font-bold mb-2">Prescription #{{ $prescription['id'] }}
                                ({{ $prescription['created_at'] }})
                            </h3> --}}

                                @foreach ($prescription['items'] as $index => $item)
                                    <!-- Single medication card -->
                                    <div class="p-3 rounded-md bg-blue-500/20 max-w-xl mb-4">
                                        <!-- العنوان والحالة -->
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-sm font-semibold">Medication {{ $index + 1 }}</span>

                                        </div>

                                        <!-- الصف الأول -->
                                        <div class="grid grid-cols-4 gap-4 text-xs text-gray-400">
                                            <div>Type</div>
                                            <div>Medical name</div>
                                            <div>Trade name</div>
                                            <div>Quantity</div>
                                        </div>
                                        <div class="grid grid-cols-4 gap-4 mt-1 text-sm text-white">
                                            <div>{{ ucfirst($item['pre_dosage_form']) }}</div>
                                            <div>{{ $item['pre_scientific'] }}</div>
                                            <div>{{ $item['pre_trade'] }}</div>
                                            <div>{{ $item['pre_total_quantity'] }}</div>
                                        </div>

                                        <!-- الصف الثاني -->
                                        <div class="grid grid-cols-3 gap-4 text-xs text-gray-400 mt-4">
                                            <div>Dosage</div>
                                            <div>Time</div>
                                            <div>Doctor</div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 mt-1 text-sm text-white">
                                            <div>{{ $item['pre_dose'] }} × {{ $item['pre_quantity_per_dose'] }}</div>
                                            <div>{{ str_replace('_', ' ', $item['pre_timing']) }}</div>
                                            <div>{{ $item['pre_prescribed_by_doctor'] }}</div>
                                        </div>

                                        <!-- البدائل -->
                                        @if (!empty($item['pre_alternatives']))
                                            <div class="flex items-center mt-4 text-xs text-gray-400">
                                                <div class="w-1/4">Alternative :</div>
                                                <div class="w-3/4 text-sm text-white">[
                                                    {{ implode(', ', $item['pre_alternatives']) }} ]</div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-400 text-sm">لا توجد وصفات لهذه الزيارة.</p>
                    @endif
                </div>
            </div>

            {{-- Material in This Visit (second list) --}}
            <div x-data="{ open: false }" class="bg-[#062E47] p-4 rounded-md">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-semibold text-lg text-white">Material in This Visit</h2>
                    <button @click="open = true"
                        class="bg-blue-500/20 border-2 border-blue-500 border-opacity-50 p-1 rounded inline-flex items-center justify-center">
                        <!-- plus icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-black" width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="inline-block text-white">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </button>
                </div>

                <!-- قائمة المواد -->
                <ul class="space-y-1 overflow-y-auto scrollbar-hide   max-h-44">
                    @forelse ($doctorMaterials ?? [] as $mat)
                        <li class="flex items-center justify-between bg-[#162a3a] p-3 rounded-xl">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <!-- الصورة -->
                                <img src="{{ asset('storage/' . $mat->material->material_image) }}" alt="Material"
                                    class="w-10 h-10 rounded-full object-cover" />

                                <!-- النصوص -->
                                <div>
                                    <p class="font-semibold text-white">{{ $mat->material->material_name }}</p>
                                    <p class="text-sm text-gray-400">الكمية: {{ $mat->dm_quantity }}</p>
                                </div>
                            </div>

                            <!-- السعر -->
                            <span class="px-3 py-1 text-xs rounded-full bg-green-600/35 text-green-600 font-semibold">
                                {{ $mat->dm_total_price }} $
                            </span>
                        </li>

                    @empty
                        <p class="text-gray-400 text-sm">لا توجد مواد مستهلكة بعد لهذه الزيارة.</p>
                    @endforelse
                </ul>



                <!-- Dialog -->
                <!-- Dialog -->
                <div x-show="open" x-cloak class="fixed inset-0 flex items-center justify-center bg-black/50">
                    <div class="bg-blue-900 text-black p-6 rounded-xl shadow-lg w-[600px]">
                        <h3 class="text-lg font-bold mb-4">إضافة مواد جديدة</h3>

                        <form method="POST" action="{{ route('doctor.materials.storeMultiple') }}">
                            @csrf
                            <input type="hidden" name="doctor_id" value="{{ auth()->user()->doctor->id }}">
                            <input type="hidden" name="visit_id" value="{{ $currentPatient['visit']['id'] ?? '' }}">

                            <!-- الحقول الديناميكية -->
                            <div x-data="{ items: [{ material_id: '', quantity: 1 }] }">
                                <template x-for="(item, index) in items" :key="index">
                                    <div class="border p-3 rounded-lg mb-3 bg-gray-50">
                                        <div class="mb-2">
                                            <label class="block text-sm">المادة</label>
                                            <select :name="'materials[' + index + '][material_id]'"
                                                x-model="item.material_id" class="w-full border rounded p-2">
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->material_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-2">
                                            <label class="block text-sm">الكمية</label>
                                            <input type="number" :name="'materials[' + index + '][quantity]'"
                                                x-model="item.quantity" class="w-full border rounded p-2" min="1">
                                        </div>
                                        <div class="mb-2">
                                            <label class="block text-sm">الجودة</label>
                                            <input type="number" :name="'materials[' + index + '][quality]'"
                                                x-model="item.quality" class="w-full border rounded p-2" min="1"
                                                max="5">
                                        </div>
                                        <button type="button" @click="items.splice(index,1)" x-show="items.length > 1"
                                            class="text-red-600 text-sm">إزالة</button>
                                    </div>
                                </template>

                                <!-- زر إضافة صف جديد -->
                                <button type="button" @click="items.push({ material_id: '', quantity: 1 })"
                                    class="bg-blue-500 text-white px-3 py-1 rounded text-sm">
                                    + إضافة مادة أخرى
                                </button>
                            </div>

                            <!-- أزرار -->
                            <div class="flex justify-end space-x-2 mt-4">
                                <button type="button" @click="open = false"
                                    class="px-3 py-1 rounded bg-gray-300">إلغاء</button>
                                <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white">حفظ</button>
                            </div>
                        </form>
                    </div>
                </div>


            </div>


            {{-- Visit price --}}
            {{-- Finish Visit Form --}}
            <div class="bg-[#062E47] p-4 rounded-md mt-4">
                <h3 class="font-semibold mb-3 text-white">إنهاء الزيارة</h3>

                {{-- عرض مجموع الاستهلاك --}}
                <div class="flex justify-between items-center mb-2 text-blue-300">
                    <span class="text-sm">Total consumption</span>
                    <span id="totalConsumption" class="bg-[#072a3a] px-3 py-1 rounded text-sm font-semibold">
                        {{ $totalConsumption ?? 0 }} $
                    </span>
                </div>

                <form id="finishVisitForm" method="POST"
                    action="{{ route('doctor.visits.finish', $currentPatient['visit']['id'] ?? 0) }}">
                    @csrf

                    {{-- إدخال سعر المعاينة --}}
                    <div class="flex justify-between items-center mb-3 text-blue-300">
                        <label class="text-sm font-semibold">Price</label>
                        <input type="number" name="v_price" min="1" step="0.01"
                            class="bg-[#0b3f58] text-white px-3 py-1 rounded text-sm font-semibold w-32"
                            value="{{ old('v_price') }}" required>
                    </div>

                    {{-- ملاحظات الطبيب --}}
                    <div class="mb-3">
                        <label class="text-sm font-semibold text-white">Notes</label>
                        <textarea name="v_notes" class="w-full p-2 text-white bg-[#062E47] rounded-md resize-none border-2 border-blue-500"
                            rows="2">{{ old('v_notes') }}</textarea>
                    </div>

                    {{-- أزرار --}}
                    <div class="flex justify-end space-x-2">
                        <button type="submit" class="px-3 py-1 rounded bg-green-600 text-white font-semibold">
                            إدخال السعر وإنهاء الزيارة
                        </button>
                    </div>

                    {{-- مكان لرسائل النجاح --}}
                    <div id="finishVisitMsg" class="mt-2 text-sm"></div>
                </form>
            </div>

            {{-- jQuery AJAX --}}
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $('#finishVisitForm').on('submit', function(e) {
                    e.preventDefault(); // منع الفورم الافتراضي

                    let form = $(this);
                    let url = form.attr('action');
                    let data = form.serialize();

                    $.ajax({
                        url: url,
                        type: 'POST', // 👈 لازم تضيف هذا
                        data: data,
                        success: function(response) {
                            if (response.success) {
                                $('#finishVisitMsg').html(`
                <div class="bg-green-600 text-white p-2 rounded">
                    ${response.message}<br>سعر الزيارة: ${response.v_price} $
                </div>
            `);
                                $('#totalConsumption').text(response.totalConsumption + ' $');
                                form.find('input, textarea, button').prop('disabled', true);
                            }
                        },
                        error: function(xhr) {
                            let errors = xhr.responseJSON?.errors;
                            let html = '<ul class="list-disc list-inside text-red-500">';
                            if (errors) {
                                $.each(errors, function(key, value) {
                                    html += `<li>${value[0]}</li>`;
                                });
                            } else {
                                html += '<li>خطأ غير متوقع</li>';
                            }
                            html += '</ul>';
                            $('#finishVisitMsg').html(html);
                        }
                    });

                });
                success: function(response) {
                    if (response.success) {
                        $('#finishVisitMsg').html(`
            <div class="bg-green-600 text-white p-2 rounded">
                ${response.message}<br>سعر الزيارة: ${response.v_price} $
            </div>
        `);
                        $('#totalConsumption').text(response.totalConsumption + ' $');
                        form.find('input, textarea, button').prop('disabled', true);
                    }
                }
            </script>





        </div>
        <style>
            /* إخفاء Scrollbar لكل المتصفحات */
            .scrollbar-hide {
                -ms-overflow-style: none;
                /* IE and Edge */
                scrollbar-width: none;
                /* Firefox */
            }

            .scrollbar-hide::-webkit-scrollbar {
                display: none;
                /* Chrome, Safari, Opera */
            }
        </style>
        <!-- Modal -->
        <!-- Modal -->
        <div id="addPrescriptionModal" class="hidden fixed inset-0 flex items-center justify-center bg-black/50 z-50">
            <div
                class="bg-blue-900 text-black rounded-lg shadow-lg w-full max-w-2xl relative max-h-[80vh] overflow-y-auto">

                <!-- Close button -->
                <button type="button" onclick="document.getElementById('addPrescriptionModal').classList.add('hidden')"
                    class="absolute top-3 right-3 text-gray-500 hover:text-black">✕</button>

                <div class="p-6">
                    <h2 class="text-lg font-bold mb-4">إضافة دواء جديد</h2>
                    @if (isset($currentPatient['visit']))
                        <form method="POST"
                            action="{{ route('doctor.prescription.store', $currentPatient['visit']['id'] ?? 0) }}"
                            class="space-y-4">
                            @csrf

                            <!-- نوع الدواء -->
                            <div>
                                <label class="block mb-1">نوع الدواء</label>
                                <select name="pre_type" class="w-full rounded border p-2" required>
                                    <option value="chronic">مزمن</option>
                                    <option value="current">حالي</option>
                                </select>
                            </div>

                            <!-- اسم الدواء -->
                            <div>
                                <label class="block mb-1">اسم الدواء</label>
                                <input type="text" name="pre_name" class="w-full rounded border p-2" required>
                            </div>

                            <!-- الاسم العلمي -->
                            <div>
                                <label class="block mb-1">الاسم العلمي</label>
                                <input type="text" name="pre_scientific" class="w-full rounded border p-2">
                            </div>

                            <!-- الاسم التجاري -->
                            <div>
                                <label class="block mb-1">الاسم التجاري</label>
                                <input type="text" name="pre_trade" class="w-full rounded border p-2">
                            </div>

                            <!-- تواريخ -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-1">تاريخ البداية</label>
                                    <input type="date" name="pre_start_date" class="w-full rounded border p-2"
                                        required>
                                </div>
                                <div>
                                    <label class="block mb-1">تاريخ الانتهاء</label>
                                    <input type="date" name="pre_end_date" class="w-full rounded border p-2">
                                </div>
                            </div>

                            <!-- التكرار -->
                            <div>
                                <label class="block mb-1">التكرار</label>
                                <select name="pre_frequency" class="w-full rounded border p-2" required>
                                    <option value="once_daily">مرة يومياً</option>
                                    <option value="twice_daily">مرتين يومياً</option>
                                    <option value="three_times_daily">ثلاث مرات يومياً</option>
                                    <option value="daily">يومياً</option>
                                    <option value="weekly">أسبوعياً</option>
                                    <option value="monthly">شهرياً</option>
                                    <option value="yearly">سنوياً</option>
                                </select>
                            </div>

                            <!-- شكل الجرعة -->
                            <div>
                                <label class="block mb-1">شكل الجرعة</label>
                                <select name="pre_dosage_form" class="w-full rounded border p-2" required>
                                    <option value="tablet">أقراص</option>
                                    <option value="capsule">كبسولات</option>
                                    <option value="pills">حبوب</option>
                                    <option value="syrup">شراب</option>
                                    <option value="liquid">سائل</option>
                                    <option value="drops">قطرات</option>
                                    <option value="sprays">بخاخ</option>
                                    <option value="patches">لصقات</option>
                                    <option value="injections">حقن</option>
                                    <option value="powder">بودرة</option>
                                </select>
                            </div>

                            <!-- الجرعة -->
                            <div>
                                <label class="block mb-1">الجرعة</label>
                                <input type="number" name="pre_dose" step="0.1" min="0.1"
                                    class="w-full rounded border p-2" required>
                            </div>

                            <!-- التوقيت -->
                            <div>
                                <label class="block mb-1">توقيت تناول الدواء</label>
                                <select name="pre_timing" class="w-full rounded border p-2" required>
                                    <option value="before_food">قبل الأكل</option>
                                    <option value="after_food">بعد الأكل</option>
                                    <option value="morning">الصبح</option>
                                    <option value="evening">المساء</option>
                                    <option value="morning_evening">صباحاً ومساءً</option>
                                </select>
                            </div>

                            <!-- التعليمات -->
                            <div>
                                <label class="block mb-1">تعليمات إضافية</label>
                                <textarea name="instructions" class="w-full rounded border p-2"></textarea>
                            </div>

                            <!-- بدائل -->
                            <div id="alternatives-wrapper">
                                <input type="text" name="pre_alternatives[]" placeholder="دواء"
                                    class="mb-2 w-full rounded border p-2">
                            </div>
                            <button type="button" id="add-alternative" class="bg-blue-500 text-white px-3 py-1 rounded">
                                إضافة بديل
                            </button>


                            <!-- زر الحفظ -->
                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-white">
                                    إضافة الدواء
                                </button>
                            </div>
                        </form>
                    @else
                        <p class="text-red-500">لا يوجد زيارة حالية لهذا المريض.</p>
                    @endif
                </div>
            </div>
        </div>
        {{--   <script>
            document.getElementById('createPrescriptionForm').addEventListener('submit', function(e) {
                // e.preventDefault(); // منع submit العادي

                let formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': formData.get('_token'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // تعديل action الفورم داخل المودال ليشير إلى الـ prescription الجديد
                            document.querySelector('#addPrescriptionModal form')
                                .action = "/doctor/" + data.id + "/prescriptions/store";

                            // فتح المودال
                            document.getElementById('addPrescriptionModal').classList.remove('hidden');
                        }
                    })
                    .catch(err => console.error(err));
            });
        </script> --}}
        {{-- JS لفتح المودال إذا كان prescription_id موجود --}}
        @if (session()->has('prescription_id'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const modal = document.getElementById('addPrescriptionModal');
                    if (modal) {
                        modal.querySelector('form').action =
                            "/doctor/{{ session('prescription_id') }}/prescriptions/store";
                        modal.classList.remove('hidden');
                    }
                });
            </script>
        @endif
        <script>
            document.getElementById('add-alternative').addEventListener('click', function() {
                const wrapper = document.getElementById('alternatives-wrapper');
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'pre_alternatives[]';
                input.placeholder = 'دواء';
                input.classList.add('mb-2', 'w-full', 'rounded', 'border', 'p-2');
                wrapper.appendChild(input);
            });
        </script>


        <script src="//unpkg.com/alpinejs" defer></script>

    @endsection
