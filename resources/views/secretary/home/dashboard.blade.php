@extends('layouts.secretary.header')

@section('content')
 <x-auth-session-status class="mb-4" :status="session('status')" />

    <a href="{{ route('secretary.doctors') }}" style="background-color: rgb(255, 0, 0);"class="btn btn-danger">Doctor</a>
    <br> <br>
    <hr> <br>
    <a href="{{ route('secretary.patients') }}" style="background-color: rgb(255, 0, 0);"class="btn btn-danger">patients</a>
    <br> <br>
    <hr> <br>


    <div class="container mx-auto px-4">

        <h1 class="text-2xl font-bold mb-6">حجوزات اليوم - {{ \Carbon\Carbon::parse($today)->translatedFormat('l d M Y') }}
        </h1>

        {{-- إحصائيات عامة --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-100 p-4 rounded-xl text-center shadow">
                <div class="text-xl font-bold text-green-700">عدد المواعيد المؤكدة</div>
                <div class="text-3xl">{{ $globalCounts->confirmed }}</div>
            </div>
            <div class="bg-blue-100 p-4 rounded-xl text-center shadow">
                <div class="text-xl font-bold text-blue-700">عدد المواعيد المكتملة</div>
                <div class="text-3xl">{{ $globalCounts->completed }}</div>
            </div>
            <div class="bg-red-100 p-4 rounded-xl text-center shadow">
                <div class="text-xl font-bold text-red-700">عدد المواعيد الملغاة</div>
                <div class="text-3xl">{{ $globalCounts->canceled }}</div>
            </div>
        </div>

        {{-- عرض الحجوزات لكل طبيب --}}
        @foreach ($doctors as $doctor)
            @if ($doctor->appointments->count())
                <div class="mb-10 bg-blue-900 rounded-xl shadow-md p-5">
                    <h2 class="text-lg font-semibold  mb-4">د. {{ $doctor->user->name }}</h2>

                    {{-- إحصائيات هذا الطبيب --}}
                    @php
                        $confirmed = $doctor->appointments->where('status', 'confirmed')->count();
                        $completed = $doctor->appointments->where('status', 'completed')->count();
                        $canceled = $doctor->appointments
                            ->whereIn('status', ['canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary'])
                            ->count();
                    @endphp

                    <div class="flex gap-4 mb-3 text-sm">
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full">مؤكدة: {{ $confirmed }}</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full">مكتملة: {{ $completed }}</span>
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full">ملغاة: {{ $canceled }}</span>
                        <span class="ml-auto px-3 py-1 bg-gray-100 text-gray-800 rounded-full">الإجمالي:
                            {{ $doctor->appointments->where('status', 'confirmed')->count() }}</span>
                    </div>

                    {{-- جدول الحجوزات --}}
                    <table class="w-full table-auto border border-gray-300 rounded-lg text-center">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-2 py-2">اسم المريض</th>
                                <th class="px-2 py-2">صورة</th>
                                <th class="px-2 py-2">الوقت</th>
                                <th class="px-2 py-2">نوع الزيارة</th>
                                <th class="px-2 py-2">حالة الوصول</th>
                                <th class="px-2 py-2">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($doctor->appointments->where('status', 'confirmed') as $appointment)
                                <tr class="border-t">
                                    <td class="py-2">{{ $appointment->patient->user->name ?? '-' }}</td>
                                    <td class="py-2">
                                        <img src=" {{ Storage::url($appointment->patient->photo) }}"
                                            class="w-12 h-12 rounded-full mx-auto">
                                    </td>
                                    <td class="py-2">{{ $appointment->start_time }} - {{ $appointment->end_time }}</td>
                                    <td class="py-2">
                                        {{ $appointment->type_visit == 'review' ? 'review' : 'appointment' }}</td>
                                    <td class="py-2">
                                        @php
                                            $statusColors = [
                                                'in_Home' => 'bg-blue-500',
                                                'on_Street' => 'bg-yellow-500',
                                                'in_Clinic' => 'bg-green-500',
                                                'at_doctor' => 'bg-purple-500',
                                                'finished' => 'bg-gray-500',
                                            ];
                                        @endphp
                                        <span
                                            class="px-3 py-1 rounded-full text-white {{ $statusColors[$appointment->location_type] ?? 'bg-gray-400' }}">
                                            {{ __($appointment->location_type) }}
                                        </span>
                                    </td>
                                    <td class="py-2">
                                        <form action="{{ route('secretary.appointment.cancel', $appointment->id) }}"
                                            method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الموعد؟');">
                                            @csrf
                                            <button type="submit"
                                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-full text-sm">
                                                إلغاء
                                            </button>
                                        </form>
                                    </td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endforeach
    </div>
@endsection
