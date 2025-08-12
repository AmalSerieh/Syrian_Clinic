@extends('layouts.secretary.header')

@section('content')
    <div class="p-6  rounded shadow space-y-8">

        <!-- معلومات المريض -->
        <div class="flex items-center space-x-4">
            @if ($patient->photo)
                <img src="{{ asset('storage/' . $patient->photo) }}" class="w-24 h-24 rounded-full">
            @else
                <div class="w-24 h-24 bg-gray-300 rounded-full flex items-center justify-center">No Photo</div>
            @endif
            <div>
                <h2 class="text-2xl font-bold">{{ $patient->user->name }}</h2>
                <p class="text-gray-600">📧 {{ $patient->user->email }}</p>
                <p class="text-gray-600">📱 {{ $patient->user->phone }}</p>
                @if ($lastVisit)
                    <p class="text-gray-500 mt-1">🗓 آخر زيارة: {{ $lastVisit->v_started_at }}</p>
                @endif
            </div>
        </div>

        <!-- المواعيد التي تنتظر التأكيد -->
        <div>
            <h3 class="text-lg font-semibold mb-3">⏳ المواعيد التي تنتظر التأكيد</h3>
            <table class="w-full border">
                <thead class="bg-yellow-300">
                    <tr>
                        <th class="p-2">التاريخ</th>
                        <th class="p-2">الوقت</th>
                        <th class="p-2"> اسم الطبيب</th>
                        <th class="p-2">الموقع</th>
                        <th class="p-2">التأكيد</th>
                        <th class="p-2">الإلغاء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pendingAppointments as $appointment)
                        <tr class="border-t text-center">
                            <td class="p-2">{{ $appointment->date }}</td>
                            <td class="p-2">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                            <td class="p-2">{{ $appointment->doctor->user->name }}</td>
                            <td class="p-2">{{ $appointment->location_type }}</td>
                            <td class="p-2">
                                <a href="{{ route('secretary.appointment.confirm', $patient['id']) }}"
                                    class="bg-green-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
                                    confirm
                                </a>

                            </td>
                            <td class="p-2">

                                <a href="{{ route('secretary.appointment.cancel', $patient['id']) }}"
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
                                    cancel
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center p-3">لا يوجد مواعيد بانتظار التأكيد</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- الزيارات المكتملة -->
        <div>
            <h3 class="text-lg font-semibold mb-3">✅ الزيارات المكتملة</h3>
            <table class="w-full border">
                <thead class="bg-green-300">
                    <tr>
                        <th class="p-2">تاريخ البداية</th>
                        <th class="p-2">تاريخ النهاية</th>
                        <th class="p-2"> اسم الطبيب</th>
                        <th class="p-2">السعر</th>
                        <th class="p-2">مدفوع</th>
                        <th class="p-2"> </th>
                        <th class="p-2">الملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($completedVisits as $visit)
                        <tr class="border-t">
                            <td class="p-2">{{ $visit->v_started_at }}</td>
                            <td class="p-2">{{ $visit->v_ended_at }}</td>
                            <td class="p-2">{{ $visit->doctor->user->name }}</td>
                            <td class="p-2">{{ $visit->v_price }}</td>
                            <td class="p-2">{{ $visit->v_paid ? 'نعم' : 'لا' }}</td>
                            <td class="p-2"></td>
                            <td class="p-2">{{ $visit->v_notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-3">لا يوجد زيارات مكتملة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- المواعيد الملغاة -->
        <div>
            <h3 class="text-lg font-semibold mb-3">❌ المواعيد الملغاة</h3>
            <table class="w-full border">
                <thead class="bg-red-300">
                    <tr>
                        <th class="p-2">التاريخ</th>
                        <th class="p-2">الوقت</th>
                        <th class="p-2"> اسم الطبيب</th>

                        <th class="p-2">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cancelledAppointments as $appointment)
                        <tr class="border-t">
                            <td class="p-2">{{ $appointment->date }}</td>
                            <td class="p-2">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                            <td class="p-2">{{ $appointment->doctor->user->name }}</td>

                            <td class="p-2">{{ $appointment->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center p-3">لا يوجد مواعيد ملغاة</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>



        <!-- السجل الطبي -->
        <div>
            <h3 class="text-lg font-semibold mb-3">📄 السجل الطبي</h3>
            @if ($medicalRecord)
                <p class="p-3 border bg-gray-800"> ✅ مكتمل</p>
            @else
                <p> ❌ غير مكتمل</p>
            @endif

        </div>

    </div>
@endsection
