@extends('layouts.secretary.header')

@section('content')
    <x-auth-session-status class="mb-8" :status="session('status')" />
    {{--  <a href="{{ route('secretary.patients') }}"
        class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-1  right-8   transition inline-block   ">
        GO BACK
    </a> --}}
    <div class="p-2 rounded shadow space-y-8  max-w-7xl mx-auto">
        <!-- معلومات المريض مع المواعيد على اليمين داخل صندوق -->
        <div class="flex justify-between items-center  p-6 rounded-lg shadow-md">
            <!-- زر الرجوع مثبت -->
            {{-- <a href="{{ route('secretary.patients') }}"
                class="absolute  right-4 bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow transition">
                ⬅ رجوع
            </a> --}}
            <!-- جهة اليسار: صورة ومعلومات المريض -->
            <div class="flex items-center  space-x-6 ">
                @if ($patient->photo)
                    <div class="relative w-[120px] h-[120px] mx-auto mb-2 flex items-center justify-center">

                        <div class="absolute w-[120px] h-[120px] rounded-full bg-blue-600 z-[1]"></div>
                        <div
                            class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full
                               border-[6px] border-blue-600 border-b-transparent
                               rotate-[-125deg] z-0">
                        </div>
                        <img src="{{ Storage::url($patient->photo) }}" alt="patient"
                            class="w-[100px] h-[100px] object-cover object-center rounded-full z-10">
                    </div>

                    {{--  <img src="{{ asset('storage/' . $patient->photo) }}"
                        class="w-28 h-28 rounded-full object-cover border-4 border-indigo-500"> --}}
                @else
                    <div
                        class="w-28 h-28 bg-gray-300 rounded-full flex items-center justify-center text-gray-500 text-lg font-semibold border-4 border-indigo-500">
                        No Photo
                    </div>
                @endif
                <div class="mt-6">
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-3">{{ $patient->user->name }}</h2>

                    <p class="text-gray-400 mb-1 flex items-center gap-2">
                        <span class="text-xl">📧</span>
                        <span>{{ $patient->user->email }}</span>
                    </p>
                    <p class="text-gray-400 mb-4 flex items-center gap-2">
                        <span class="text-xl">📱</span>
                        <span>{{ $patient->user->phone }}</span>
                    </p>
                    <p class="text-gray-400 mb-4 flex items-center gap-2">
                        <span class="text-xl">📄</span>
                        <span>
                            Medical Record :
                            @if ($medicalRecord)
                                <span class="p-3 border bg-gray-800 text-white rounded-lg inline-bloc">

                                    ✅ complete
                                </span>
                            @else
                                <span class="inline-block border border-gray-400 rounded-lg px-2 py-1 ml-2">
                                    ❌ discomplete
                                </span>
                            @endif
                        </span>
                    </p>



                    @if ($lastVisit)
                        <p class="text-gray-500 italic text-sm">
                            🗓 آخر زيارة: {{ \Carbon\Carbon::parse($lastVisit->v_started_at)->format('d/m/Y') }}
                        </p>
                    @endif
                </div>

            </div>

            <!-- جهة اليمين: المواعيد وعددها داخل صندوق -->
            <div class=" border border-gray-300 rounded-lg p-5 max-w-xs w-full shadow-sm text-right space-y-3">


                @if ($todayAppointment)
                    <p class="text-green-700 font-semibold text-lg">📅 لديك موعد اليوم: <br> <span
                            class="font-bold">{{ \Carbon\Carbon::parse($todayAppointment->date)->format('d/m/Y') }}</span>
                    </p>
                    <p class="text-green-600 text-sm">{{ $todayAppointment->start_time }} -
                        {{ $todayAppointment->end_time }}</p>
                @elseif($nextAppointment)
                    <p class="text-blue-500 font-semibold text-lg">📅 أقرب موعد: <br> <span
                            class="font-bold">{{ \Carbon\Carbon::parse($nextAppointment->date)->format('d/m/Y') }}</span>
                    </p>
                    <p class="text-blue-400 text-sm">{{ $nextAppointment->start_time }} - {{ $nextAppointment->end_time }}
                    </p>
                @else
                    <p class="text-gray-500 font-medium">لا يوجد مواعيد مستقبلية</p>
                @endif

                <hr class="border-gray-300" />

                <p class="text-red-600 font-semibold">❌ عدد المواعيد الملغاة: <span
                        class="font-bold">{{ $cancelledAppointmentsCount }}</span></p>
                <p class="text-green-600 font-semibold">✅ عدد الزيارات المكتملة: <span
                        class="font-bold">{{ $completedVisitsCount }}</span></p>
            </div>
        </div>

        <!-- المواعيد التي تنتظر التأكيد -->
        <div>
            <h3 class="text-2xl font-semibold mb-4 text-yellow-600">⏳ المواعيد التي تنتظر التأكيد</h3>
            <table class="w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-yellow-200 text-yellow-900 font-semibold">
                    <tr>
                        <th class="p-3 border-r border-yellow-300">التاريخ</th>
                        <th class="p-3 border-r border-yellow-300">الوقت</th>
                        <th class="p-3 border-r border-yellow-300">اسم الطبيب</th>
                        <th class="p-3 border-r border-yellow-300">الموقع</th>
                        <th class="p-3 border-r border-yellow-300">التأكيد</th>
                        <th class="p-3">الإلغاء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingAppointments as $appointment)
                        <tr class="text-center border-t border-yellow-300  transition">
                            <td class="p-3">{{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                            <td class="p-3">{{ $appointment->doctor->user->name }}</td>
                            <td class="p-3">{{ ucfirst($appointment->location_type) }}</td>
                            <td class="p-3">
                                <form action="{{ route('secretary.appointment.confirm', $appointment->id) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="bg-green-500 hover:bg-green-600 text-white py-1 px-4 rounded transition">
                                        Confirm
                                    </button>
                                </form>
                            </td>
                            <td class="p-3">
                                <form action="{{ route('secretary.appointment.cancel', $appointment->id) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white py-1 px-4 rounded transition">
                                        Cancel
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500 italic">لا يوجد مواعيد بانتظار التأكيد
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- الزيارات المكتملة -->
        <div>
            <h3 class="text-2xl font-semibold mb-4 text-green-700">✅ الزيارات المكتملة</h3>
            <table class="w-full border border-green-300 rounded-lg overflow-hidden">
                <thead class="bg-green-200 text-green-900 font-semibold">
                    <tr>
                        <th class="p-3 border-r border-green-300">تاريخ البداية</th>
                        <th class="p-3 border-r border-green-300">تاريخ النهاية</th>
                        <th class="p-3 border-r border-green-300">اسم الطبيب</th>
                        <th class="p-3 border-r border-green-300">السعر</th>
                        <th class="p-3 border-r border-green-300">مدفوع</th>
                        <th class="p-3 border-green-300"></th>
                        <th class="p-3">الملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($completedVisits as $visit)
                        <tr class="border-t border-green-300 hover:bg-green-50 transition">
                            <td class="p-3">{{ \Carbon\Carbon::parse($visit->v_started_at)->format('d/m/Y') }}</td>
                            <td class="p-3">{{ \Carbon\Carbon::parse($visit->v_ended_at)->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $visit->doctor->user->name }}</td>
                            <td class="p-3">{{ number_format($visit->v_price, 2) }} ₪</td>
                            <td class="p-3">{{ $visit->v_paid ? 'نعم' : 'لا' }}</td>
                            <td class="p-3"></td>
                            <td class="p-3">{{ $visit->v_notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-500 italic">لا يوجد زيارات مكتملة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- المواعيد الملغاة -->
        <div>
            <h3 class="text-2xl font-semibold mb-4 text-red-600">❌ المواعيد الملغاة</h3>
            <table class="w-full border border-red-300 rounded-lg overflow-hidden">
                <thead class="bg-red-200 text-red-900 font-semibold">
                    <tr>
                        <th class="p-3 border-r border-red-300">التاريخ</th>
                        <th class="p-3 border-r border-red-300">الوقت</th>
                        <th class="p-3 border-r border-red-300">اسم الطبيب</th>
                        <th class="p-3">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cancelledAppointments as $appointment)
                        <tr class="border-t border-red-300  transition">
                            <td class="p-3">{{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}</td>
                            <td class="p-3">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                            <td class="p-3">{{ $appointment->doctor->user->name }}</td>
                            <td class="p-3">{{ ucfirst(str_replace('_', ' ', $appointment->status)) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500 italic">لا يوجد مواعيد ملغاة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- السجل الطبي -->
        {{--         <div>
            <h3 class="text-2xl font-semibold mb-4">📄 السجل الطبي</h3>
            @if ($medicalRecord)
                <p class="p-3 border bg-gray-800 text-white rounded-lg inline-block">✅ مكتمل</p>
            @else
                <p class="p-3 border border-gray-400 rounded-lg inline-block text-gray-600">❌ غير مكتمل</p>
            @endif
        </div> --}}

    </div>
@endsection
