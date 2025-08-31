@extends('layouts.secretary.header')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    @php
        $patientsJson = $onStreetPatients->map(fn($item) => ['doctor_id' => $item->doctor_id])->toJson();
    @endphp
    <div class="p-6 space-y-5">
        <!-- رسائل التنبيه -->
        <div class="fixed top-20 right-4 z-50 w-96 space-y-2">
            @if (session('status'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    <p>{{ session('status') }}</p>
                    @if (session('notification_warning'))
                        <p class="text-yellow-600 text-sm mt-2">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            {{ session('notification_warning') }}
                        </p>
                    @endif
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-900/90 border-l-4 border-red-500 text-white rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                        <div>{{ session('error') }}</div>
                    </div>
                </div>
            @endif
        </div>
        <div class="grid grid-cols-3 gap-6  ">
            {{-- قسم الثلثين --}}
            <div class="col-span-2 space-y-2 -ml-5">
                {{-- Today's Sales --}}
                <div class="bg-[#2F80ED33] p-4 rounded-xl w-full h-[200px] space-y-1">
                    <h6 class="text-lg font-semibold text-white ">Today's Sales {{ $today }}</h6>
                    <div class="relative">
                        <!-- زر التمرير لليسار -->
                        <button
                            class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded-full z-10 scroll-left-btn hidden md:block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M15 18l-6-6 6-6" />
                            </svg>
                        </button>

                        <!-- حاوية العناصر مع التمرير -->
                        <div class="flex overflow-x-auto scroll-smooth space-x-4 py-2 px-1 hide-scrollbar"
                            id="stats-container">
                            <div class="bg-[#060E0E] p-4 rounded-xl text-white min-w-[175px] flex-shrink-0">
                                <!-- محتوى البطاقة الأولى -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-signal-icon lucide-signal text-orange-300">
                                    <path d="M2 20h.01" />
                                    <path d="M7 20v-4" />
                                    <path d="M12 20v-8" />
                                    <path d="M17 20V8" />
                                    <path d="M22 4v16" />
                                </svg>
                                <p class="font-bold text-lg">$5k</p>
                                <p>Total Sales</p>
                                <span class="text-orange-300 text-xs">+10% from yesterday</span>
                            </div>

                            <!-- باقي البطاقات بنفس الهيكل -->
                            <div class="bg-[#060E0E] p-4 rounded-xl text-white min-w-[175px] flex-shrink-0">
                                <!-- محتوى البطاقة الأولى -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-clipboard-check-icon lucide-clipboard-check text-blue-300">
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                    <path d="m9 14 2 2 4-4" />
                                </svg>
                                <p class="font-bold text-lg">$5k</p>
                                <p>Total Sales</p>
                                <span class="text-orange-300 text-xs">+10% from yesterday</span>
                            </div>
                            <!-- باقي البطاقات بنفس الهيكل -->
                            <div class="bg-[#060E0E] p-4 rounded-xl text-white min-w-[175px] flex-shrink-0">
                                <!-- محتوى البطاقة الأولى -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-clipboard-check-icon lucide-clipboard-check text-blue-300">
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                    <path d="m9 14 2 2 4-4" />
                                </svg>
                                <p class="font-bold text-lg">$5k</p>
                                <p>Total Sales</p>
                                <span class="text-orange-300 text-xs">+10% from yesterday</span>
                            </div>
                            <!-- باقي البطاقات بنفس الهيكل -->
                            <div class="bg-[#060E0E] p-4 rounded-xl text-white min-w-[175px] flex-shrink-0">
                                <!-- محتوى البطاقة الأولى -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-clipboard-check-icon lucide-clipboard-check text-blue-300">
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                    <path d="m9 14 2 2 4-4" />
                                </svg>
                                <p class="font-bold text-lg">$5k</p>
                                <p>Total Sales</p>
                                <span class="text-orange-300 text-xs">+10% from yesterday</span>
                            </div>
                            <!-- باقي البطاقات بنفس الهيكل -->
                            <div class="bg-[#060E0E] p-4 rounded-xl text-white min-w-[175px] flex-shrink-0">
                                <!-- محتوى البطاقة الأولى -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-weight-icon lucide-weight text-pink-200">
                                    <circle cx="12" cy="5" r="3" />
                                    <path
                                        d="M6.5 8a2 2 0 0 0-1.905 1.46L2.1 18.5A2 2 0 0 0 4 21h16a2 2 0 0 0 1.925-2.54L19.4 9.5A2 2 0 0 0 17.48 8Z" />
                                </svg>
                                <p class="font-bold text-lg">$5k</p>
                                <p>Total Sales</p>
                                <span class="text-orange-300 text-xs">+10% from yesterday</span>
                            </div>
                            <!-- باقي البطاقات بنفس الهيكل -->
                            <div class="bg-[#060E0E] p-4 rounded-xl text-white min-w-[175px] flex-shrink-0">
                                <!-- محتوى البطاقة الأولى -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-package-plus-icon lucide-package-plus text-blue-500">
                                    <path d="M16 16h6" />
                                    <path d="M19 13v6" />
                                    <path
                                        d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                                    <path d="m7.5 4.27 9 5.15" />
                                    <polyline points="3.29 7 12 12 20.71 7" />
                                    <line x1="12" x2="12" y1="22" y2="12" />
                                </svg>
                                <p class="font-bold text-lg">$5k</p>
                                <p>Total Sales</p>
                                <span class="text-blue-300 text-xs">+10% from yesterday</span>
                            </div>

                            <!-- يمكنك إضافة المزيد من البطاقات هنا -->
                        </div>

                        <!-- زر التمرير لليمين -->
                        <button
                            class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-800 text-white p-2 rounded-full z-10 scroll-right-btn hidden md:block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M9 18l6-6-6-6" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="bg-[#2F80ED33] p-4 rounded-xl  " >

                    {{-- Dates Table --}}
                    <h2 class="text-lg font-semibold mb-4">Dates</h2>
                    @if ($DoctorPatients->isEmpty())
                        @if ($upcomingAppointments->isEmpty())
                            <div class="text-center text-gray-400 py-6 text-lg">
                                No upcoming appointments found
                            </div>
                        @else
                            <table class="w-full text-left">
                                <thead class="text-gray-400 text-sm">
                                    <tr>
                                        <th class="py-2">Patient</th>
                                        <th class="py-2">Doctor</th>
                                        <th class="py-2">Type</th>
                                        <th class="py-2">Date</th>
                                        <th class="py-2">Time</th>

                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#0E2A3F]">
                                    @foreach ($upcomingAppointments as $appointment)
                                        <tr>
                                            <td class="py-2 flex items-center gap-2">
                                                <img src="{{ asset('storage/' . $appointment->patient->photo) }}"
                                                    alt="{{ $appointment->patient->user->name }}"
                                                    class="rounded-full w-8 h-8">
                                                {{ $appointment->patient->user->name }}
                                            </td>
                                            <td class="py-2">Dr. {{ $appointment->doctor->user->name }}</td>
                                            <td class="py-2">{{ $appointment->type_visit }}</td>
                                            <td class="py-2">
                                                {{ \Carbon\Carbon::parse($appointment->date)->format('Y-m-d') }}
                                                @if ($appointment->date == $today)
                                                    <span class="text-xs text-green-400">(Today)</span>
                                                @endif
                                            </td>
                                            <td class="py-2">
                                                {{ \Carbon\Carbon::parse($appointment->start_time)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($appointment->end_time)->format('H:i') }}
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $upcomingAppointments->links() }}
                            </div>
                        @endif
                    @else
                        <table class="w-full text-left">
                            <thead class="text-gray-400 text-sm">
                                <tr>
                                    <th class="py-2">Patient</th>
                                    <th class="py-2">Type of visit</th>
                                    <th class="py-2">Doctor</th>
                                    <th class="py-2">Start Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#0E2A3F]">
                                @foreach ($DoctorPatients as $DoctorPatient)
                                    <tr>
                                        <td class="py-2 flex items-center gap-2">
                                            <div class="flex items-center gap-2">
                                                <img src="{{ asset('storage/' . $DoctorPatient->patient->photo) }}"
                                                    alt="{{ $DoctorPatient->patient->user->name }}"
                                                    class="rounded-full w-8 h-8">
                                            </div>
                                            {{ $DoctorPatient->patient->user->name }}
                                        </td>
                                        <td class="py-2">{{ $DoctorPatient->type_visit }}</td>
                                        <td class="py-2"> Dr.{{ $DoctorPatient->doctor->user->name }}</td>
                                        <td class="py-2">
                                            {{ optional($DoctorPatient->waitinglist->first())->w_start_time ? \Carbon\Carbon::parse($DoctorPatient->waitinglist->first()->w_start_time)->format('H:i') : '---' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </div>

                <div class="  w-full">
                    <div class="flex gap-4">
                        {{-- Add Doctor Buttons --}}
                        <div class="flex flex-col gap-4 w-1/2">
                            <a href="{{ route('secretary.patient.book.add') }}"
                                class="border-2 border-dashed border-blue-600 p-4 rounded-3xl text-center text-white block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-2 text-blue-500"
                                    width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-notebook-pen-icon lucide-notebook-pen">
                                    <path d="M13.4 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-7.4" />
                                    <path d="M2 6h4" />
                                    <path d="M2 10h4" />
                                    <path d="M2 14h4" />
                                    <path d="M2 18h4" />
                                    <path
                                        d="M21.378 5.626a1 1 0 1 0-3.004-3.004l-5.01 5.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z" />
                                </svg>
                                <p>Add Appointment</p>
                            </a>
                            <a href="{{ route('secretary.patient.add') }}"
                                class="border-2 border-dashed border-orange-600 p-4 rounded-3xl text-center text-orange block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-2 text-orange-500"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M2 21a8 8 0 0 1 13.292-6" />
                                    <circle cx="10" cy="8" r="5" />
                                    <path d="M19 16v6" />
                                    <path d="M22 19h-6" />
                                </svg>
                                <p>Add Patient</p>
                            </a>
                        </div>



                        {{-- النص اليمين (Notes for me) --}}
                        <div x-data="notesComponent()" class="w-[50%] bg-[#2F80ED33] p-4 rounded-xl text-white">
                            {{-- Header --}}
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold">Notes for me</h2>
                                <button @click="showModal = true"
                                    class="bg-[#114B6B] px-2 py-1 rounded text-xs">+</button>
                            </div>

                            {{-- Notes List --}}
                            <ul class="space-y-2 overflow-y-auto max-h-48 " style="-ms-overflow-style:none;scrollbar-width:none;">
                                <template x-for="(note, index) in notes" :key="index">
                                    <li class="flex justify-between items-center bg-[#0E2A3F] p-2 rounded">
                                        <span x-text="note"></span>
                                        <button class="text-red-400" @click="deleteNote(index)">
                                            {{-- Trash icon --}}
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-trash2-icon lucide-trash-2">
                                                <path d="M10 11v6" />
                                                <path d="M14 11v6" />
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />
                                                <path d="M3 6h18" />
                                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                            </svg>
                                        </button>
                                    </li>
                                </template>
                            </ul>

                            {{-- Modal --}}
                            <div x-show="showModal"
                                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-[#0E2A3F] p-6 rounded w-80 space-y-4">
                                    <h3 class="text-lg font-semibold text-white">Add New Note</h3>
                                    <input x-model="newNote" type="text" class="w-full p-2 rounded text-black"
                                        placeholder="اكتب ملاحظتك هنا">
                                    <div class="flex justify-end gap-2">
                                        <button @click="showModal = false"
                                            class="px-3 py-1 rounded bg-gray-600 text-white">Cancel</button>
                                        <button @click="addNote()"
                                            class="px-3 py-1 rounded bg-[#114B6B] text-white">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

            </div>



            {{-- قسم الثلث --}}
            <div class="col-span-1 space-y-4">
                {{-- Patients in the clinic --}}

                <div class="bg-[#2F80ED33] p-6 rounded-xl " x-data="{
                    selectedDoctorClinic: 'all',
                    visiblePatients: 2,
                    showAll: false,
                    get filteredPatients() {
                        const allPatients = Alpine.store('patients') || [];
                        if (this.selectedDoctorClinic === 'all') {
                            return allPatients;
                        }
                        return allPatients.filter(p => p.doctor_id == this.selectedDoctorClinic);
                    },
                    hasPatients() {
                        return this.filteredPatients.length > 0;
                    }
                }">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Patients in Clinic Waiting List</h2>
                        <div class="relative">
                            <select x-model="selectedDoctorClinic"
                                class="bg-[#062E47] text-blue-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 pr-1">
                                <option value="all">All Doctors</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">Dr. {{ $doctor->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="overflow-y-auto" :class="!showAll ? 'max-h-[140px]' : ''">
                            <ul class="space-y-4">
                                <template x-if="!hasPatients()">
                                    <li class="text-center py-4 text-gray-400">
                                        There are no patients with this doctor.
                                    </li>
                                </template>

                                <template x-for="(patient, index) in filteredPatients" :key="patient.id">
                                    <li x-show="showAll || index < visiblePatients"
                                        class="flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <img :src="`/storage/${patient.patient.photo}`" class="rounded-full w-8 h-8"
                                                :alt="patient.patient.user.name">
                                            <div>
                                                <p x-text="patient.patient.user.name"></p>
                                                <p class="text-xs text-gray-400"
                                                    x-text="`Dr. ${patient.doctor.user.name}`"></p>
                                            </div>

                                            <div class="text-right text-blue-200 text-sm space-y-1">
                                                <template
                                                    x-if="patient.waiting_list && patient.waiting_list.w_check_in_time">
                                                    <p class="text-sm text-green-500"
                                                        x-text="new Date(patient.waiting_list.w_check_in_time).toLocaleTimeString()">
                                                    </p>
                                                </template>
                                                <template
                                                    x-if="!(patient.waiting_list && patient.waiting_list.w_check_in_time)">
                                                    <p class="text-sm text-gray-500">لم يسجل بعد</p>
                                                </template>
                                            </div>
                                        </div>
                                        {{-- <div class="text-right">
                                            <button @click="cancelAppointment(patient.id)"
                                                class="text-blue-400 text-xs bg-transparent border border-blue-400 px-2 py-1 rounded hover:bg-blue-400 hover:text-white transition">
                                                Cancel
                                            </button>
                                        </div> --}}
                                    </li>

                                </template>
                            </ul>
                        </div>


                        <template x-if="hasPatients() && filteredPatients.length > visiblePatients">
                            <div class="text-center mt-2">
                                <button @click="showAll = !showAll" class="text-blue-400 text-xs hover:underline">
                                    <span x-text="showAll ? 'Show Less' : 'Show More'"></span>
                                    (<span
                                        x-text="showAll ? visiblePatients : filteredPatients.length - visiblePatients"></span>)
                                </button>
                            </div>
                        </template>
                    </div>
                </div>


                {{-- Patients on the streeet --}}
                <!-- المرضى على الطريق -->
                <!-- المرضى على الطريق مع فلترة -->


                {{-- ثم في الكود: --}}
                <div x-data="{
                    selectedDoctor: 'all',
                    patients: {{ $patientsJson }},
                    hasPatients(doctorId) {
                        if (doctorId === 'all') {
                            return this.patients.length > 0;
                        }
                        return this.patients.some(p => p.doctor_id == doctorId);
                    }
                }" class="bg-[#062E47] p-6 rounded-xl">

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Patients on the street</h2>

                        <!-- فلترة حسب الطبيب -->
                        <div class="relative">
                            <select x-model="selectedDoctor"
                                class="bg-[#2F80ED33] text-blue-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 pr-1">
                                <option value="all">All Doctors</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">Dr. {{ $doctor->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <ul class="space-y-4 overflow-y-auto max-h-[180px] pr-2 scrollbar-hide">
                        <template x-if="!hasPatients(selectedDoctor)">
                            <li class="text-center py-4 text-gray-400">
                                There are no patients with this doctor.
                            </li>
                        </template>

                        @foreach ($onStreetPatients as $onStreetPatient)
                            <li x-show="selectedDoctor === 'all' || selectedDoctor === '{{ $onStreetPatient->doctor_id }}'"
                                class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('storage/' . $onStreetPatient->patient->photo) }}"
                                        class="rounded-full w-8 h-8" alt="{{ $onStreetPatient->patient->user->name }}">
                                    <div>
                                        <p>{{ $onStreetPatient->patient->user->name }}</p>
                                        <p class="text-xs text-gray-400">Dr. {{ $onStreetPatient->doctor->user->name }}
                                        </p>
                                    </div>

                                    <div class="text-right text-blue-200 text-sm space-y-1">
                                        <p>{{ $onStreetPatient->start_time }} - {{ $onStreetPatient->end_time }}</p>
                                        <p class="text-xs">
                                            {{ $onStreetPatient->arrivved_time ?? 'غير محدد' }} time
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <form
                                        action="{{ route('secretary.patient.appointments.moveto.clinic', $onStreetPatient->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-blue-400 text-xs bg-transparent border border-blue-400 px-2 py-1 rounded hover:bg-blue-400 hover:text-white transition">
                                            Move to Clinic
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                </div>
                {{-- More patients --}}
                <div x-data="{
                    selectedDoctorPayment: 'all',
                    patientsClinic: {{ $patientsJsonPayment }},
                    hasPatientsPayment(doctorId) {
                        if (doctorId === 'all') {
                            return this.patientsClinic.length > 0;
                        }
                        return this.patientsClinic.some(p => p.doctor_id == doctorId);
                    }
                }" class="bg-[#062E47] p-6 rounded-xl ">

                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold">Patients in Clinic Payment </h2>

                        <!-- فلترة حسب الطبيب -->
                        <div class="relative">
                            <select x-model="selectedDoctorPayment"
                                class="bg-[#062E47] text-blue-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-1 pr-1">
                                <option value="all">All Doctors</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}">Dr. {{ $doctor->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <ul class="space-y-4 overflow-y-auto max-h-[180px] scrollbar-hide pr-2">
                        <template x-if="!hasPatientsPayment(selectedDoctorPayment)">
                            <li class="text-center py-4 text-gray-400">
                                There are no patients with this doctor.
                            </li>
                        </template>

                        @foreach ($paymentPatients as $paymentPatient)
                            <li x-show="selectedDoctorPayment === 'all' || selectedDoctorPayment === '{{ $paymentPatient->doctor_id }}'"
                                class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <img src="{{ asset('storage/' . $paymentPatient->patient->photo) }}"
                                        class="rounded-full w-8 h-8" alt="{{ $paymentPatient->patient->user->name }}">
                                    <div>
                                        <p>{{ $paymentPatient->patient->user->name }}</p>
                                        <p class="text-xs text-gray-400">
                                            Dr. {{ $paymentPatient->doctor->user->name }}
                                        </p>
                                    </div>

                                    <div class="text-right text-blue-200 text-sm space-y-1">
                                        <p class="text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($paymentPatient->visit->first()->v_ended_at)->format('H:i:s') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    {{ $paymentPatient->visit->first()->v_price }}
                                    <form
                                        action="{{ route('secretary.patient.appointments.moveto.clinic', $paymentPatient->id) }}"
                                        method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="text-blue-400 text-xs bg-transparent border border-blue-400 px-2 py-1 rounded hover:bg-blue-400 hover:text-white transition">
                                            Confirm Pay
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                </div>
                {{-- Debug Doctor Schedules --}}
                <div class="hidden">
                    <h3>Debug Info:</h3>
                    <p>Selected Doctor ID: {{ $doctorId }}</p>
                    <p>Selected Doctor: {{ $selectedDoctor ? $selectedDoctor->user->name : 'None' }}</p>
                    <p>Doctor Schedules:</p>
                    <ul>
                        @if ($selectedDoctor && $selectedDoctor->doctorSchedule)
                            @foreach ($selectedDoctor->doctorSchedule as $schedule)
                                <li>
                                    Day: {{ $schedule->day }},
                                    Hours: {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                </li>
                            @endforeach
                        @else
                            <li>No schedule data</li>
                        @endif
                    </ul>
                </div>
                {{-- Calendar --}}
                @php
                    use Carbon\Carbon;

                    $startOfMonth = $currentDate->copy()->startOfMonth();
                    $endOfMonth = $currentDate->copy()->endOfMonth();
                    $daysInMonth = $currentDate->daysInMonth;
                    $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)

                    // تحضير أيام الدوام للطبيب المحدد
                    $workingDayNames = [];
                    if ($selectedDoctor && $selectedDoctor->doctorSchedule) {
                        $workingDayNames = $selectedDoctor->doctorSchedule->pluck('day')->toArray();
                    }
                @endphp

                <div class="bg-[#062E47] p-6 rounded-xl text-white">
                    {{-- Header with navigation --}}
                    <div class="flex justify-between items-center mb-10 gap-2">
                        <!-- أزرار التنقل -->
                        <div class="flex items-center gap-2">
                            <a href="?date={{ $prevMonth }}&doctor_id={{ $doctorId }}"
                                class="text-sm px-2 py-1 bg-gray-700 rounded hover:bg-gray-600 flex-shrink-0">&larr;</a>
                            <h3 class="text-sm font-semibold whitespace-nowrap">
                                {{ $currentDate->format('F Y') }}
                            </h3>
                            <a href="?date={{ $nextMonth }}&doctor_id={{ $doctorId }}"
                                class="text-sm px-2 py-1 bg-gray-700 rounded hover:bg-gray-600 flex-shrink-0">&rarr;</a>
                        </div>

                        <!-- فلترة الطبيب - النسخة المعدلة -->
                        <form method="GET" class="relative flex-1 min-w-0">
                            <select name="doctor_id" onchange="this.form.submit()"
                                class="bg-[#062E47] text-blue-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-full p-1 pr-3 appearance-none">
                                <option value="">Select Doctor</option>
                                @foreach ($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ $doctorId == $doctor->id ? 'selected' : '' }}>
                                        Dr. {{ $doctor->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute right-2 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <input type="hidden" name="date" value="{{ $currentDate->toDateString() }}">
                        </form>
                    </div>


                    {{-- Week Days --}}
                    <div class="grid grid-cols-7 gap-2 text-center text-xs text-gray-300 mb-2">
                        @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                            <span @if (in_array($day, $workingDayNames)) class="font-bold text-yellow-300" @endif>
                                {{ substr($day, 0, 3) }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Days --}}
                    <div class="grid grid-cols-7 gap-2 text-center text-sm">
                        {{-- Empty slots before month starts --}}
                        @for ($i = 0; $i < $startDayOfWeek; $i++)
                            <span class="p-1 opacity-50"></span>
                        @endfor

                        {{-- Days of the month --}}
                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $dayDate = Carbon::create($currentDate->year, $currentDate->month, $day);
                                $isToday = Carbon::now()->isSameDay($dayDate);
                                $dayName = $dayDate->format('l'); // اسم اليوم كامل (مثلا 'Monday')

                                // تحقق إذا الطبيب يداوم بهذا اليوم
                                $isDoctorWorking = in_array($dayName, $workingDayNames);
                                $workingHours = '';

                                if ($isDoctorWorking && $selectedDoctor) {
                                    $schedule = $selectedDoctor->doctorSchedule->firstWhere('day', $dayName);
                                    $workingHours = $schedule->start_time . ' - ' . $schedule->end_time;
                                }

                                // تحديد الفئات CSS
                                $classes = 'p-1 rounded-full cursor-default ';

                                if ($isToday && $isDoctorWorking) {
                                    $classes .= 'bg-blue-500 text-white';
                                    $tooltip = "Today - Working Hours: $workingHours";
                                } elseif ($isToday) {
                                    $classes .= 'bg-green-500 text-white';
                                    $tooltip = 'Today';
                                } elseif ($isDoctorWorking) {
                                    $classes .= 'bg-yellow-500 text-black';
                                    $tooltip = "Working Hours: $workingHours";
                                } else {
                                    $tooltip = '';
                                }
                            @endphp

                            <span class="{{ $classes }}"
                                @if ($tooltip) title="{{ $tooltip }}"
                      x-tooltip.placement.bottom="'{{ $tooltip }}'" @endif>
                                {{ $day }}
                            </span>
                        @endfor
                    </div>
                </div>

            </div>
        </div>

    </div>



    <script>
        function notesComponent() {
            return {
                showModal: false,
                newNote: '',
                notes: JSON.parse(localStorage.getItem('my_notes') || '[]'),

                addNote() {
                    if (this.newNote.trim() !== '') {
                        this.notes.push(this.newNote);
                        this.saveNotes();
                        this.newNote = '';
                        this.showModal = false;
                    }
                },

                deleteNote(index) {
                    this.notes.splice(index, 1);
                    this.saveNotes();
                },

                saveNotes() {
                    localStorage.setItem('my_notes', JSON.stringify(this.notes));
                }
            }
        }
        document.addEventListener('alpine:init', () => {
            Alpine.store('patients', @json($waitingPatients));
        });
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('stats-container');
            const scrollLeftBtn = document.querySelector('.scroll-left-btn');
            const scrollRightBtn = document.querySelector('.scroll-right-btn');

            // إظهار/إخفاء أزرار التمرير حسب الحاجة
            function updateScrollButtons() {
                scrollLeftBtn.style.display = container.scrollLeft > 0 ? 'block' : 'none';
                scrollRightBtn.style.display = container.scrollLeft < container.scrollWidth - container
                    .clientWidth ? 'block' : 'none';
            }

            // التمرير عند النقر على الأزرار
            scrollLeftBtn.addEventListener('click', () => {
                container.scrollBy({
                    left: -200,
                    behavior: 'smooth'
                });
            });

            scrollRightBtn.addEventListener('click', () => {
                container.scrollBy({
                    left: 200,
                    behavior: 'smooth'
                });
            });

            // تحديث حالة الأزرار عند التمرير
            container.addEventListener('scroll', updateScrollButtons);

            // تحديث أولي للأزرار
            updateScrollButtons();
        });
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection
