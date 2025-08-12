<!-- resources/views/doctor/appointments/clinic-patients.blade.php -->

@extends('layouts.doctor.header')

@section('content')
    <h2 class="text-xl font-bold mb-4">🏥 المرضى المنتظرون في العيادة</h2>

    @if ($waitingPatients->isEmpty())
        <p class="text-gray-500">لا يوجد مرضى في قائمة الانتظار حاليًا.</p>
    @else
        <table class="w-full mt-3 table-auto border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">المريض</th>
                    <th class="border px-4 py-2">وقت الدخول</th>
                    <th class="border px-4 py-2">نوع الزيارة</th>
                    <th class="border px-4 py-2">عرض السجل الطبي</th>
                    <th class="border px-4 py-2">إجراء</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($waitingPatients as $entry)
                    @php
                        $appointment = $entry->appointment;
                        $patient = $appointment->patient ?? null;
                    @endphp

                    @if ($appointment && $patient)
                        <tr>
                            <td class="border px-4 py-2">{{ $patient->user->name }}</td>
                            <td class="border px-4 py-2">{{ $entry->check_in_time }}</td>
                            <td class="border px-4 py-2">{{ $appointment->type_visit }}</td>
                            <td class="border px-4 py-2">
                                <a href="{{ route('doctor.patients.medicalRecord.show', $patient->id) }}"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1 rounded">
                                    عرض السجل الطبي
                                </a>
                            </td>
                            <td class="border px-4 py-2">
                                <a href="{{ route('doctor.patients.prescription.create', $patient->id) }}"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1 rounded">
                                    أدخل الوصفة الطبية
                                </a>
                            </td>
                            <td class="border px-4 py-2">
                                <form method="POST" action="{{ route('doctor.appointments.enterConsultation', $appointment) }}">
                                    @csrf
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded">
                                        إدخال المريض
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
