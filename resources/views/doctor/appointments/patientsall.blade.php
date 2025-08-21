{{-- resources/views/doctor/patients/index.blade.php --}}
@extends('layouts.doctor.header')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">📋 المرضى لديهم مواعيد مؤكدة اليوم وما بعده</h1>

    @if($patients->isEmpty())
        <p class="text-gray-500">لا يوجد مرضى لديهم مواعيد مؤكدة اليوم.</p>
    @else
        <table class="min-w-full bg-white border rounded-lg overflow-hidden">
            <thead class="bg-blue-500 text-white">
                <tr>
                    <th class="p-4 text-left">Name</th>
                    <th class="p-4 text-left">Email</th>
                    <th class="p-4 text-left">Phone</th>
                    <th class="p-4 text-left">Action</th>
                    <th class="p-4 text-left">Show Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $patient)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4">{{ $patient->user->name }}</td>
                        <td class="p-4">{{ $patient->user->email }}</td>
                        <td class="p-4">{{ $patient->user->phone ?? '-' }}</td>
                        <td class="p-4">
                            {{-- يمكن وضع أي إجراء آخر --}}
                            <a href="#" class="text-blue-600 hover:underline">Message</a>
                        </td>
                        <td class="p-4">
                            <a href="{{ route('doctor.patients.medicalRecord.show', $patient->id) }}"
                               class="text-green-600 hover:underline font-semibold">
                                عرض التفاصيل
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
