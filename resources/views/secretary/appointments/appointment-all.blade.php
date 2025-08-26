@extends('layouts.secretary.header')

@section('content')
    <div class="space-y-4">
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
        {{--  <x-auth-session-status class="mb-8" :status="session('status')" /> --}}

        <!-- Appointment Stats Cards -->
        <div class="grid grid-cols-3 gap-4 -mt-2 text-white">
            <!-- Done -->
            <div
                class="bg-green-900/30 border border-green-700 p-5 rounded-xl shadow flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="bg-green-700 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-green-300 text-sm">Total Done Date</h2>
                        <p class="text-xl font-bold text-green-200">{{ $totalDone }} Date</p>
                    </div>
                </div>
            </div>

            <!-- Canceled -->
            <div class="bg-red-900/30 border border-red-700 p-5 rounded-xl shadow flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="bg-red-700 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-red-300 text-sm">Total Cancel Date</h2>
                        <p class="text-xl font-bold text-red-200">{{ $totalCanceled }} Date</p>
                    </div>
                </div>

            </div>

            <!-- All Dates -->
            <div
                class="bg-yellow-900/30 border border-yellow-700 p-5 rounded-xl shadow flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="bg-yellow-700 p-2 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-yellow-300 text-sm">Total Date</h2>
                        <p class="text-xl font-bold text-yellow-200">{{ $totalAppointments }} Date</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Doctor Sections --}}
        @foreach ($doctors as $doctor)
            @php
                $doctorAppointments = $appointments->where('doctor_id', $doctor->id);
                $confirmedCount = $doctorAppointments->where('status', 'confirmed')->count();
                $canceledCount = $doctorAppointments
                    ->whereIn('status', ['canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary'])
                    ->count();
                $doctorBookingRequests = $bookingRequests->where('doctor_id', $doctor->id);
                $canCancelAll =
                    $doctorAppointments
                        ->where('date', '>=', \Carbon\Carbon::today())
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->count() > 0;
            @endphp
            <div class="grid grid-cols-3 gap-4">
                {{-- Dates Table  #2F80ED20  --}}

                <div class="col-span-2 bg-[#062E47] p-4 rounded-2xl">
                    <h2 class="text-lg mb-2">
                        Dates - <span class="text-blue-400">{{ $doctorAppointments->count() }}</span>
                        <span class="text-gray-400">date at day</span>
                        <span class="float-right text-blue-500 font-bold">Dr. {{ $doctor->user->name }}</span>
                    </h2>
                    <div class="relative overflow-hidden">
                        <div class="overflow-x-auto overflow-y-visible">
                            <table class=" w-full text-sm text-left text-white">
                                <thead>
                                    <tr class="text-gray-400  border-gray-600">
                                        <th>Name</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Doctor</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Location</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($doctorAppointments->isEmpty())
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-gray-400">
                                                There are no appointments for this doctor at the moment.
                                            </td>
                                        </tr>
                                    @else
                                        @foreach ($doctorAppointments as $appointment)
                                            <tr class=" border-gray-700 ">
                                                <td class="py-3 flex items-center gap-2">
                                                    @if ($appointment['photo'])
                                                        @if ($appointment->patient->photo)
                                                            <img src="{{ asset('storage/' . $appointment->patient->photo) }}"
                                                                class="w-12 h-12 rounded-full object-cover border-2 border-slate-700"
                                                                alt="صورة المريض">
                                                        @else
                                                            <div
                                                                class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                                                                -
                                                            </div>
                                                        @endif
                                                    @endif
                                                    {{ $appointment->patient->user->name }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('D, d M Y') }}
                                                </td>
                                                <td>{{ $appointment->start_time }}-{{ $appointment->end_time }}</td>
                                                <td>Dr. {{ $doctor->user->name }}</td>
                                                <td>{{ $appointment->type_visit }}</td>
                                                <td>
                                                    @if ($appointment->status == 'confirmed')
                                                        <span
                                                            class="text-green-300 border border-green-300 rounded-3xl px-2 py-1 bg-green-900/60">
                                                            Confirmed
                                                        </span>
                                                    @elseif($appointment->status == 'canceled_by_patient')
                                                        <span
                                                            class="text-red-200 border border-red-300 rounded-3xl px-2 py-1 bg-red-900/60">
                                                            Canceled by pateint
                                                        </span>
                                                    @elseif($appointment->status == 'canceled_by_doctor')
                                                        <span
                                                            class="text-red-200 border border-red-300 rounded-3xl px-2 py-1 bg-red-900/60">
                                                            Canceled by doctor
                                                        </span>
                                                    @elseif($appointment->status == 'canceled_by_secretary')
                                                        <span
                                                            class="text-red-200 border border-red-300 rounded-3xl px-2 py-1 bg-red-900/60">
                                                            Canceled by secretary
                                                        </span>
                                                    @elseif($appointment->status == 'pending')
                                                        <span
                                                            class="text-[#FC9700] border border-[#FC9700] rounded-3xl px-2 py-1  bg-[#FC970026]">
                                                            Waiting
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $appointment->location_type }}</td>
                                                <td>
                                                    @if (!in_array($appointment->status, ['canceled_by_patient', 'canceled_by_doctor', 'canceled_by_secretary']))
                                                        <form method="POST"
                                                            action="{{ route('secretary.appointment.cancel', $appointment->id) }}">
                                                            @csrf
                                                            <button
                                                                class="text-red-500 border border-red-500 bg-red-800/30 px-2 py-1 rounded-3xl">
                                                                Cancel
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="text-gray-400 italic">No Action</span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Booking Requests --}}
                <div class="bg-[#062E47] p-4 rounded-2xl text-white">

                    <h2 class="text-lg mb-4">Booking requests</h2>
                    <div class="space-y-2">
                        @if (isset($doctorBookingRequests) && $doctorBookingRequests->count() > 0)
                            @foreach ($doctorBookingRequests as $request)
                                <div class="flex items-center justify-between bg-[#0E2A3F] p-2 rounded-xl">

                                    <div class="flex items-center gap-2">
                                        <img src="{{ asset('storage/' . $request->patient->photo) }}"
                                            class="w-10 h-10 rounded-full object-cover border-2 border-slate-700"
                                            alt="صورة المريض">
                                        <div class="flex flex-col">

                                            <span>{{ $request->patient->user->name }} wants booking</span>
                                            <span class="text-xs text-gray-500">({{ $request->date }})</span>
                                        </div>
                                    </div>

                                    <div class="flex gap-1 textxs">
                                        {{-- {{ dd($appointment) }} --}}
                                        <form method="POST"
                                            action="{{ route('secretary.appointment.confirm', $appointment->id) }}">
                                            @csrf
                                            <button
                                                class="text-green-300 border border-green-300  bg-green-900/60 px-2 py-1 rounded-3xl">Confirm</button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('secretary.appointment.cancel', $appointment->id) }}">
                                            @csrf
                                            <button
                                                class="text-red-500  border border-red-500 bg-red-800/30  px-2 py-1 rounded-3xl">Cancel</button>

                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-gray-400 bg-[#0E2A3F] p-4 rounded-xl">
                                There are no reservation requests currently.
                            </div>
                        @endif

                    </div>
                    @php
                        $slot = $nearestSlots[$doctor->id];
                    @endphp
                    <!-- Nearest Date Booking Section -->
                    <div class="mt-4 bg-[#072C3D] p-4 rounded-xl text-white text-sm">
                        <div class="flex items-center justify-between mb-2">
                            <div class="border-2 border-blue-200 bg-blue-500/10 px-4 py-2 rounded-2xl">
                                <p class="text-gray-300">The nearest empty date</p>
                                @if ($nearestSlots[$doctor->id]['status'] === 'available')
                                    <p class="text-blue-400 font-semibold text-base mt-1">
                                        {{ \Carbon\Carbon::parse($nearestSlots[$doctor->id]['date'])->format('D, d M Y') }}
                                        ({{ $nearestSlots[$doctor->id]['time'] }})
                                    </p>
                                @else
                                    <p class="text-red-400 font-semibold text-base mt-1">لا يوجد موعد متاح</p>
                                @endif

                            </div>

                            <!-- Book Button -->
                            <!-- Icon -->
                            @if (isset($slot['date']) && isset($slot['time']))
                                <a href="{{ route('secretary.appointments.book', [
                                    'doctor_id' => $doctor->id,
                                    'date' => $nearestSlots[$doctor->id]['date'],
                                    'time' => $nearestSlots[$doctor->id]['time'],
                                ]) }}"
                                    class="flex flex-col items-center border-2 border-dashed border-blue-500 bg-blue-500/10 px-4 py-2 rounded-xl hover:bg-blue-500/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mb-1 text-blue-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs font-semibold">Book</span>
                                </a>
                            @else
                                <span class="text-gray-400 text-sm">غير متاح</span>
                            @endif

                        </div>
                        <div class="flex justify-between gap-2 mt-4">
                            <!-- Cancel Button -->
                            @if ($canCancelAll)
                                <form action="{{ route('secretary.appointments.cancelAll', $doctor->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من إلغاء جميع المواعيد القادمة؟');"
                                    class="w-1/2">
                                    @csrf
                                    <button type="submit"
                                        class="w-full border-2 border-red-500 bg-red-500/10 px-4 py-2 rounded-xl text-center hover:bg-red-500/20 focus:outline-none">
                                        <div class="text-red-400 text-lg font-bold">Cancel</div>
                                        <div class="text-xs font-semibold">{{ $canceledCount }} Date</div>
                                    </button>
                                </form>
                            @else
                                <button disabled
                                    class="w-1/2 border-2 border-gray-500 bg-gray-500/10 px-4 py-2 rounded-xl text-center cursor-not-allowed">
                                    <div class="text-gray-400 text-lg font-bold">No Action</div>
                                    <div class="text-xs font-semibold">0 Date</div>
                                </button>
                            @endif



                            <!-- Done Button -->
                            <button
                                class="w-1/2 border-2  border-green-500 bg-green-500/10  px-4 py-2 rounded-xl text-center hover:bg-green-500/20">
                                <div class="text-green-400 text-lg font-bold">Done</div>
                                <div class="text-xs font-semibold">{{ $confirmedCount }} Date</div>
                            </button>
                        </div>

                    </div>

                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script>
        function confirmAppointment(appointmentId) {
            fetch(`/secretary/appointment/${appointmentId}/confirm1`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(resp => {
                    const statusCell = document.querySelector(`#status-${appointmentId}`);
                    const messageCell = document.querySelector(`#message-${appointmentId}`);

                    if (resp.status) {
                        // تحديث الحالة مباشرة
                        if (statusCell) {
                            statusCell.innerText = 'confirmed';
                            statusCell.classList.remove('text-yellow-500');
                            statusCell.classList.add('text-green-500');
                        }

                        // عرض التحذير إذا وجد
                        if (messageCell) {
                            messageCell.innerHTML = resp.message || '';
                            messageCell.classList.add('text-sm');
                            if (resp.message.includes('ملاحظة')) {
                                messageCell.classList.add('text-yellow-500');
                            }
                        }
                    } else {
                        if (messageCell) {
                            messageCell.innerHTML = resp.message || 'حدث خطأ أثناء تأكيد الموعد';
                            messageCell.classList.add('text-sm', 'text-red-500');
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('خطأ في الاتصال بالسيرفر');
                });
        }
    </script>
@endsection
