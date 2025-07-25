@extends('layouts.secretary.header')

@section('content')
    <x-slot name="header">
        <h2 class="text-xl font-bold">📅 حجوزات المرضى لكل طبيب</h2>
    </x-slot>


    <div class="p-6">
        <h2 class="text-xl font-bold mb-4">🩺 مواعيد الطبيب: {{ $data['doctor_name'] }}</h2>

        <div class="mb-4">
            <p>✅ عدد المواعيد المؤكدة: {{ count($data['confirmed_appointments']) }}</p>
        </div>

        <h3 class="text-lg font-semibold mt-4 mb-2">📆 تفاصيل المواعيد المؤكدة:</h3>

        <table class="min-w-full bg-red rounded shadow">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-2 px-4 text-center">المريض</th>
                    <th class="py-2 px-4 text-center">📅 التاريخ</th>
                    <th class="py-2 px-4 text-center">⏰ من</th>
                    <th class="py-2 px-4 text-center">⏱️ إلى</th>
                    <th class="py-2 px-4 text-center">📍 الموقع</th>
                     <th class="py-2 px-4 text-center">📍 مدة الوصول للعيادة</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['confirmed_appointments'] as $appointment)
                    <tr class="border-b border-gray-200 text-center">
                        <td class="py-2 px-4">{{ $appointment['patient_name'] }}</td>
                        <td class="py-2 px-4">{{ $appointment['date'] }}</td>
                        <td class="py-2 px-4">{{ $appointment['start_time'] }}</td>
                        <td class="py-2 px-4">{{ $appointment['end_time'] }}</td>
                        <td class="py-2 px-4">{{ $appointment['location'] }}</td>
                        <td class="py-2 px-4">{{ $appointment['arrivved_time'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-2 px-4 text-center text-gray-500">لا توجد مواعيد مؤكدة.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
