@extends('layouts.secretary.header')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div class="p-6 space-y-6">
        <div class="grid grid-cols-3 gap-6 -mt-8 ">
            {{-- قسم الثلثين --}}
            <div class="col-span-2 space-y-2 -ml-5">
                {{-- Today's Sales --}}
                <div class="bg-[#062E47] p-4 rounded-xl w-full h-[185px] space-y-2">

                    <h6 class="text-lg font-semibold text-white ">Today's Sales</h6>
                    <div class="flex flex-wrap gap-4 text-sm ">

                        <div class="bg-gray-900 p-4 rounded-xl text-white w-[175px] ">
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
                            <span class="text-orange-300 text-xs ">+10% from yesterday</span>
                        </div>
                        <div class="bg-gray-900 p-4 rounded-xl text-white w-[175px]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-clipboard-check-icon lucide-clipboard-check text-blue-300">
                                <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                <path d="m9 14 2 2 4-4" />
                            </svg>
                            <p class="font-bold text-lg">500</p>
                            <p>Total Orders</p>
                            <span class="text-blue-300 text-xs">+6% from yesterday</span>
                        </div>
                        <div class="bg-gray-900 p-4 rounded-xl text-white w-[175px]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-weight-icon lucide-weight text-pink-200">
                                <circle cx="12" cy="5" r="3" />
                                <path
                                    d="M6.5 8a2 2 0 0 0-1.905 1.46L2.1 18.5A2 2 0 0 0 4 21h16a2 2 0 0 0 1.925-2.54L19.4 9.5A2 2 0 0 0 17.48 8Z" />
                            </svg>
                            <p class="font-bold text-lg">9</p>
                            <p>Product Sold</p>
                            <span class="text-pink-200 text-xs">+2% from yesterday</span>
                        </div>
                        <div class="bg-gray-900 p-4 rounded-xl text-white w-[175px]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-package-plus-icon lucide-package-plus text-blue-500">
                                <path d="M16 16h6" />
                                <path d="M19 13v6" />
                                <path
                                    d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14" />
                                <path d="m7.5 4.27 9 5.15" />
                                <polyline points="3.29 7 12 12 20.71 7" />
                                <line x1="12" x2="12" y1="22" y2="12" />
                            </svg>
                            <p class="font-bold text-lg">12</p>
                            <p>New Customers</p>
                            <span class="text-blue-500 text-xs">+3% from yesterday</span>
                        </div>
                    </div>
                </div>

                <div class="bg-[#062E47] p-4 rounded-xl ">

                    {{-- Dates Table --}}
                    <h2 class="text-lg font-semibold mb-4">Dates</h2>
                    <table class="w-full text-left">
                        <thead class="text-gray-400 text-sm">
                            <tr>
                                <th class="py-2">Name</th>
                                <th class="py-2">Date</th>
                                <th class="py-2">Doctor</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#0E2A3F]">
                            <tr>
                                <td class="py-2 flex items-center gap-2">
                                    <div class="flex items-center gap-2">
                                        <img src="data:image/jpeg;base64,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"
                                            class="rounded-full w-8 h-8" alt="User">
                                        <!-- Optionally add a dropdown arrow, username etc. -->
                                    </div>
                                    Adobe After Effect
                                </td>
                                <td class="py-2">Sat, 20 Apr 2020</td>
                                <td class="py-2">$80.09</td>
                                <td class="py-2">
                                    <span class="text-green-400 text-xs">Deposited</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 flex items-center gap-2">
                                    <div class="flex items-center gap-2">
                                        <img src="data:image/jpeg;base64,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"
                                            class="rounded-full w-8 h-8" alt="User">
                                        <!-- Optionally add a dropdown arrow, username etc. -->
                                    </div>
                                    Mcdonald's
                                </td>
                                <td class="py-2">Fri, 19 Apr 2020</td>
                                <td class="py-2">$7.03</td>
                                <td class="py-2">
                                    <span class="text-green-400 text-xs">Deposited</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 flex items-center gap-2">
                                    <div class="flex items-center gap-2">
                                        <img src="data:image/jpeg;base64,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"
                                            class="rounded-full w-8 h-8" alt="User">
                                        <!-- Optionally add a dropdown arrow, username etc. -->
                                    </div>
                                    Levi's
                                </td>
                                <td class="py-2">Tue, 19 Apr 2020</td>
                                <td class="py-2">$30.09</td>
                                <td class="py-2">
                                    <span class="text-green-400 text-xs">Deposited</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 flex items-center gap-2">
                                    <div class="flex items-center gap-2">
                                        <img src="data:image/jpeg;base64,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"
                                            class="rounded-full w-8 h-8" alt="User">
                                        <!-- Optionally add a dropdown arrow, username etc. -->
                                    </div>
                                    Adobe After Effect
                                </td>
                                <td class="py-2">Sat, 20 Apr 2020</td>
                                <td class="py-2">$80.09</td>
                                <td class="py-2">
                                    <span class="text-green-400 text-xs">Deposited</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 flex items-center gap-2">
                                    <div class="flex items-center gap-2">
                                        <img src="data:image/jpeg;base64,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"
                                            class="rounded-full w-8 h-8" alt="User">
                                        <!-- Optionally add a dropdown arrow, username etc. -->
                                    </div>
                                    Mcdonald's
                                </td>
                                <td class="py-2">Fri, 19 Apr 2020</td>
                                <td class="py-2">$7.03</td>
                                <td class="py-2">
                                    <span class="text-green-400 text-xs">Deposited</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="  w-full">
                    <div class="flex gap-4">
                        {{-- Add Doctor Buttons --}}
                        <div class="flex flex-col gap-4 w-1/2">
                            <a href="#"
                                class="border-2 border-dashed border-blue-600 p-6 rounded-3xl text-center text-white block">
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
                                class="border-2 border-dashed border-blue-600 p-6 rounded-3xl text-center text-white block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 mx-auto mb-2 text-blue-500"
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
                        <div x-data="notesComponent()" class="w-[50%] bg-[#062E47] p-4 rounded-xl text-white">
                            {{-- Header --}}
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold">Notes for me</h2>
                                <button @click="showModal = true"
                                    class="bg-[#114B6B] px-2 py-1 rounded text-xs">+</button>
                            </div>

                            {{-- Notes List --}}
                            <ul class="space-y-2">
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
                <div class="bg-[#062E47] p-6 rounded-xl">
                    <h2 class="text-lg font-semibold mb-4">Patients in the clinic</h2>
                    <ul class="space-y-4">
                        <li class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <img src="data:image/jpeg;base64,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"
                                    class="rounded-full w-8 h-8" alt="">
                                Adobe After Effect
                            </div>
                            <div class="text-right">
                                <p>$80.09</p>
                                <span class="text-red-400 text-xs">Finished</span>
                            </div>
                        </li>
                        <li class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <img src="data:image/jpeg;base64,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"
                                    class="rounded-full w-8 h-8" alt="">
                                Mcdonald's
                            </div>
                            <div class="text-right">
                                <p>$80.09</p>
                                <span class="text-yellow-400 text-xs">Waiting</span>
                            </div>
                        </li>
                        <li class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <img src="data:image/jpeg;base64,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"
                                    class="rounded-full w-8 h-8" alt="">
                                Levi's
                            </div>
                            <div class="text-right">
                                <p>$7.03</p>
                                <span class="text-green-400 text-xs">Deposited</span>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- More patients --}}
                <div class="bg-[#062E47] p-6 rounded-xl">
                    <h2 class="text-lg font-semibold mb-4">Patients in the clinic</h2>
                    <ul class="space-y-4">
                        <li class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <img src="data:image/jpeg;base64,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"
                                    class="rounded-full w-8 h-8" alt="">
                                Levi's
                            </div>
                            <div class="text-right">
                                <p>$7.03</p>
                                <span class="text-blue-400 text-xs">Pay</span>
                            </div>
                        </li>
                        <li class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <img src="data:image/jpeg;base64,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"
                                    class="rounded-full w-8 h-8" alt="">
                                Levi's
                            </div>
                            <div class="text-right">
                                <p>$7.03</p>
                                <span class="text-blue-400 text-xs">Pay</span>
                            </div>
                        </li>
                    </ul>
                </div>

                {{-- Calendar --}}
                @php
                    use Carbon\Carbon;

                    $currentDate = request('date') ? Carbon::parse(request('date')) : Carbon::now();
                    $startOfMonth = $currentDate->copy()->startOfMonth();
                    $endOfMonth = $currentDate->copy()->endOfMonth();
                    $daysInMonth = $currentDate->daysInMonth;
                    $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)

                    // Previous & Next month for navigation
                    $prevMonth = $currentDate->copy()->subMonth()->toDateString();
                    $nextMonth = $currentDate->copy()->addMonth()->toDateString();
                @endphp

                <div class="bg-[#062E47] p-6 rounded-xl text-white">
                    {{-- Header with navigation --}}
                    <div class="flex justify-between items-center mb-4">
                        <a href="?date={{ $prevMonth }}"
                            class="text-sm px-2 py-1 bg-gray-700 rounded hover:bg-gray-600">&larr;</a>
                        <h2 class="text-lg font-semibold">
                            {{ $currentDate->format('F Y') }}
                        </h2>
                        <a href="?date={{ $nextMonth }}"
                            class="text-sm px-2 py-1 bg-gray-700 rounded hover:bg-gray-600">&rarr;</a>
                    </div>

                    {{-- Week Days --}}
                    <div class="grid grid-cols-7 gap-2 text-center text-xs text-gray-300 mb-2">
                        <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                    </div>

                    {{-- Days --}}
                    <div class="grid grid-cols-7 gap-2 text-center text-sm">
                        {{-- Empty slots before month starts --}}
                        @for ($i = 0; $i < $startDayOfWeek; $i++)
                            <span></span>
                        @endfor

                        {{-- Real days --}}
                        @for ($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $isToday = Carbon::now()->isSameDay(
                                    Carbon::create($currentDate->year, $currentDate->month, $day),
                                );
                            @endphp
                            <span class="{{ $isToday ? 'bg-red-500 text-white rounded-full' : '' }} p-1">
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
                notes: ['ملاحظة ١', 'ملاحظة ٢'],

                addNote() {
                    if (this.newNote.trim() !== '') {
                        this.notes.push(this.newNote);
                        this.newNote = '';
                        this.showModal = false;
                    }
                },

                deleteNote(index) {
                    this.notes.splice(index, 1);
                }
            }
        }
    </script>
@endsection
