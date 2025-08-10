<!-- resources/views/secretary/today_appointments.blade.php -->

<x-app-layout>
    <h2 class="text-xl font-bold mb-4">📅 قائمة المواعيد المؤكدة اليوم</h2>

    @php
        function statusColor($status)
        {
            return match ($status) {
                'confirmed' => 'text-blue-600',
                'on_Street' => 'text-orange-500',
                'in_Clinic' => 'text-green-600',
                'at_Doctor' => 'text-purple-600',
                'in_Payment' => 'text-yellow-600',
                'finished' => 'text-gray-500',
                default => 'text-black',
            };
        }
        function statusLabel($status)
        {
            return match ($status) {
                'confirmed' => '✅ مؤكد',
                'on_Street' => '🚗 في الطريق',
                'in_Clinic' => '🏥 في العيادة',
                'at_Doctor' => '🩺 في المعاينة',
                'in_Payment' => '💵 الدفع',
                'finished' => '✅ مكتمل',
                default => '❓ غير معروف',
            };
        }
    @endphp

    @forelse($appointments as $doctorId => $doctorAppointments)
        <div class="mb-8 p-4 border rounded-lg shadow">
            <h3 class="text-lg font-semibold text-blue-600">
                🩺 الدكتور: {{ $doctorAppointments->first()->doctor->user->name }}
            </h3>

            <table class="w-full mt-3 table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2">المريض</th>
                        <th class="border px-4 py-2">الوقت</th>
                        <th class="border px-4 py-2">مدة الوصول</th>
                        <th class="border px-4 py-2">موقع المريض</th>
                        <th class="border px-4 py-2">نوع الزيارة</th>
                        <th class="border px-4 py-2">الحالة</th>
                        <th class="border px-4 py-2">إجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($doctorAppointments as $appointment)
                        <tr>
                            <td class="border px-4 py-2">{{ $appointment->patient->user->name }}</td>
                            <td class="border px-4 py-2">{{ $appointment->start_time }} - {{ $appointment->end_time }}
                            </td>
                            <td class="border px-4 py-2">{{ $appointment->arrivved_time }} دقيقة</td>
                            <td class="border px-4 py-2">{{ $appointment->location_type }}</td>
                            <td class="border px-4 py-2">{{ $appointment->type_visit }}</td>
                            <td class="border px-4 py-2 font-bold ">{{ $appointment->status }}</td>
                            <td class="border px-4 py-2">
                                @if (in_array($appointment->status, ['confirmed', 'on_Street']))
                                    <form method="POST"
                                        action="{{ route('secretary.appointments.inClinic', $appointment) }}">
                                        @csrf
                                        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded">
                                            وصل المريض
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="text-gray-500">لا يوجد مواعيد مؤكدة اليوم.
            {{Auth::user()->secretary->id}}
        </div>
    @endforelse
</x-app-layout>
