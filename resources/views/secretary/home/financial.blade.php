@extends('layouts.secretary.header')

@section('content')
    <div class="flex gap-6">

        {{-- يسار: الأطباء + الكروت فوق --}}
        <div class="w-3/4 flex flex-col gap-6">

            {{-- الكروت فوق --}}

            <div class="grid grid-cols-2 gap-6 mb-6 w-full">
                <!-- الاستهلاك -->
                <div
                    class="bg-green-900/30 border border-green-700 p-5 rounded-xl shadow flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="bg-green-700 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-green-300 text-sm">إجمالي الاستهلاك</h2>
                            <p class="text-xl font-bold text-green-200">
                                {{ $totals[$selectedPeriod]['consumption'] ?? 0 }} $
                            </p>
                        </div>
                    </div>
                </div>

                <!-- الدخل -->
                <div
                    class="bg-red-900/30 border border-red-700 p-5 rounded-xl shadow flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-700 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-red-300 text-sm">إجمالي الدخل المدخل للعيادة</h2>
                            <p class="text-xl font-bold text-red-200">
                                {{ $totals[$selectedPeriod]['income'] ?? 0 }} $
                            </p>
                        </div>
                    </div>
                </div>
        </div>




        <!-- ✅ اختيار الفترة -->
        <div class="flex justify-end mb-6">
            <form method="GET" action="{{ route('secretary.finance') }}">
                <select name="period" onchange="this.form.submit()"
                    class="border rounded-lg px-3 py-2 bg-gray-700 text-white">
                    <option value="today" {{ $selectedPeriod == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ $selectedPeriod == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $selectedPeriod == 'month' ? 'selected' : '' }}>This Month</option>
                </select>
            </form>
        </div>

        {{-- ✅ Grid الأطباء --}}
        <div class="grid grid-cols-3 gap-6 -mt-8">
            @foreach ($doctors as $doctor)
                @php
                    $data = $results[$doctor->id][$selectedPeriod] ?? null;
                @endphp

                @if ($data)
                    <div class="bg-gray-800 rounded-xl p-4 text-center shadow-lg">
                        <!-- صورة + ديكور -->
                        <div class="relative w-[110px] h-[110px] mx-auto mb-2 flex items-center justify-center">
                            <div class="absolute w-[100px] h-[100px] rounded-full bg-blue-500 z-[1]"></div>
                            <div
                                class="absolute w-[130px] h-[130px] -top-[10px] -left-[10px] rounded-full border-[10px] border-blue-500 border-r-transparent border-b-transparent rotate-[-30deg] z-0">
                            </div>
                            <img src="{{ asset('storage/' . $doctor->photo) }}" alt="{{ $doctor->user->name }}"
                                class="w-[80px] h-[80px] object-cover object-center rounded-full border-[3px] border-black bg-white z-[2] relative">
                        </div>
                        <h3 class="mt-3 text-xl font-bold">{{ $doctor->user->name }}</h3>

                        <div class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-green-400">Total income</span>
                                <span class="text-green-400 font-bold">{{ number_format($data['income'], 2) }} $</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-red-400">Consumption</span>
                                <span class="text-red-400 font-bold">{{ number_format($data['consumption'], 2) }}
                                    $</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-yellow-400">Bills Share</span>
                                <span class="text-yellow-400 font-bold">{{ number_format($data['bills_share'], 2) }}
                                    $</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-purple-400">Secretary Share</span>
                                <span class="text-purple-400 font-bold">{{ number_format($data['secretary_share'], 2) }}
                                    $</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-pink-400">Nurse Salary</span>
                                <span class="text-pink-400 font-bold">{{ number_format($data['nurse_salary'], 2) }}
                                    $</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-orange-400">Deduction</span>
                                <span class="text-orange-400 font-bold">{{ number_format($data['deduction'], 2) }}
                                    $</span>
                            </div>
                            <div class="border-t border-white pt-2 flex justify-between">
                                <span class="text-blue-400">Net Balance</span>
                                <span class="text-blue-400 font-bold">{{ number_format($data['net_balance'], 2) }}
                                    $</span>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

    </div>

    {{-- يمين: العمود --}}
    <div class="w-1/4 flex flex-col">
        {{-- الصندوق الخاص بـ Shared Costs --}}
        <div class="bg-gray-800 rounded-xl p-4 flex-1">
            <h3 class="font-bold mb-3">Shared Costs</h3>
            <ul class="flex flex-col justify-between text-gray-300">
                <li class="flex justify-between">
                    <span>Secretary's Salary:</span>
                    <span class="text-blue-500">150.46 $</span>
                </li>
                 <li class="flex justify-between">
                    <span>ربح العيادة هذا الشهر:</span>
                    <span class="text-blue-500">{{ $totals['month']['profit'] }} $</span>
                </li>


                @foreach ($clinicBills as $bill)
                    <li class="flex justify-between">
                        <span>{{ $bill->description }}</span>
                        <span class="text-blue-500">{{ number_format($bill->amount, 2) }} $</span>
                    </li>
                @endforeach
            </ul>
        </div>


        {{-- الأزرار تحت الصندوق --}}
        <div class="mt-4 space-y-3">
            <div x-data="{ open: false }">
                <!-- زر فتح النافذة -->
                <button @click="open = true"
                    class="w-full h-14 rounded border-2 border-dashed border-blue-600 px-4 text-blue-600 hover:bg-blue-600/20 hover:text-black transition-colors duration-300">
                    Add An Invoice
                </button>

                <!-- نافذة الإدخال -->
                <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black/50 z-50" x-transition>
                    <div class="bg-red text-black rounded-xl p-6 w-96">
                        <h2 class="text-lg font-bold mb-4">➕ Add Invoice</h2>

                        <form action="{{ route('clinic.bills.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="block text-sm font-medium">Description</label>
                                <input type="text" name="description" class="w-full border rounded px-3 py-2" required>
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm font-medium">Amount ($)</label>
                                <input type="number" step="0.01" name="amount" class="w-full border rounded px-3 py-2"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm font-medium">Date</label>
                                <input type="date" name="billed_at" class="w-full border rounded px-3 py-2">
                            </div>

                            <div class="flex justify-end space-x-2">
                                <button type="button" @click="open = false"
                                    class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- قسم عرض الفواتير -->
                {{-- <div class="bg-gray-800 rounded-xl p-4 flex-1 mt-6">
                        <h3 class="font-bold mb-3">Shared Costs</h3>
                        <ul class="flex flex-col justify-between text-gray-300">
                            @foreach ($clinicBills as $bill)
                                <li class="flex justify-between">
                                    <span>{{ $bill->description }}</span>
                                    <span class="text-blue-500">{{ number_format($bill->amount, 2) }} $</span>
                                </li>
                            @endforeach
                        </ul>
                    </div> --}}
            </div>


            {{--   <button
                    class="w-full h-14 rounded border-2 border-dashed border-green-600 px-4 text-green-600 hover:bg-green-600/20 hover:text-black transition-colors duration-300">
                    Add A User
                </button> --}}

        </div>
    </div>

    </div>
@endsection
