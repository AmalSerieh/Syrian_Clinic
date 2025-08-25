@extends('layouts.doctor.header')

@section('content')
    <div class="overflow-x-auto rounded-2xl">
        <x-auth-session-status class="mb-8" :status="session('status')" />
        @if (session('error'))
            <div class="p-4 bg-red-600/70 border-l-4 border-red-500 text-white rounded-lg shadow-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            </div>
        @endif

        <div class="p-6 bg-[#0B1622] min-h-screen text-white rounded-3xl">
            <!-- إحصائيات سريعة -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-[#0f2538] p-4 rounded-2xl text-center">
                    <div class="text-2xl font-bold text-blue-400">{{ $patients->count() }}</div>
                    <div class="text-gray-400">عدد المرضى</div>
                </div>
                <div class="bg-[#0f2538] p-4 rounded-2xl text-center">
                    <div class="text-2xl font-bold text-green-400">{{ $appointments->count() }}</div>
                    <div class="text-gray-400">إجمالي المواعيد</div>
                </div>
                <div class="bg-[#0f2538] p-4 rounded-2xl text-center">
                    @php
                        $todayAppointments = $appointments
                            ->where('date', \Carbon\Carbon::today()->format('Y-m-d'))
                            ->count();
                    @endphp
                    <div class="text-2xl font-bold text-yellow-400">{{ $todayAppointments }}</div>
                    <div class="text-gray-400">مواعيد اليوم</div>
                </div>
            </div>
            <br>
            <h1 class="text-2xl font-bold mb-6">📋 جميع المواعيد المؤكدة (اليوم وما بعده)</h1>

            @if ($appointments->isEmpty())
                <p class="text-gray-500">لا يوجد مواعيد مؤكدة اليوم أو في المستقبل.</p>
            @else
                <!-- قسم جميع المواعيد المرتبة -->
                <div class="overflow-x-auto rounded-2xl">
                    <table class="min-w-full text-sm text-center table-auto">
                        <thead class="bg-[#0f2538] text-gray-400">
                            <tr>
                                <th class="p-4">المريض</th>
                                <th class="p-4">التاريخ</th>
                                <th class="p-4">الوقت</th>
                                <th class="p-4">البريد الإلكتروني</th>
                                <th class="p-4">الهاتف</th>
                                <th class="p-4">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#1b2d42]">
                            @foreach ($appointments->sortBy(['date', 'start_time']) as $appointment)
                                <tr class="transition text-white text-center hover:bg-[#0f2538]">
                                    <!-- صورة واسم المريض -->
                                    <td class="p-4 flex items-center justify-center gap-3 font-medium">
                                        @if ($appointment->patient->photo)
                                            <img src="{{ asset('storage/' . $appointment->patient->photo) }}"
                                                class="w-10 h-10 rounded-full object-cover border-2 border-slate-700"
                                                alt="صورة المريض">
                                        @else
                                            <div
                                                class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center">
                                                <span class="text-xs">-</span>
                                            </div>
                                        @endif
                                        {{ $appointment->patient->user->name }}
                                    </td>

                                    <!-- تاريخ الموعد -->
                                    <td class="p-4">
                                        <span class="font-medium text-green-600">
                                            {{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('Y/m/d') }}
                                        </span>
                                    </td>

                                    <!-- وقت الموعد -->
                                    <td class="p-4">
                                        <span class="text-blue-400">
                                            {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }}-
                                            {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                        </span>
                                    </td>

                                    <!-- البريد الإلكتروني -->
                                    <td class="p-4">
                                        {{ $appointment->patient->user->email }}
                                    </td>

                                    <!-- الهاتف -->
                                    <td class="p-4">
                                        {{ $appointment->patient->user->phone ?? 'غير متوفر' }}
                                    </td>

                                    <!-- الإجراءات -->
                                    <td class="p-4">
                                        <div class="flex justify-center space-x-2">
                                            <!-- زر عرض السجل الطبي في Modal -->
                                           {{--  <button onclick="openMedicalRecordModal({{ $appointment->patient->id }})"
                                                class="bg-blue-700 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition">
                                                📋 السجل الطبي
                                            </button> --}}
                                            <a href="{{route('doctor.patients.medicalRecord.show',$appointment->patient->id)}}"
                                            class="bg-blue-700 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs transition">📋 السجل الطبي</a>

                                            <form
                                                action="{{ route('doctor.patients.appointment.cancel', $appointment->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="bg-red-700 hover:bg-red-600 text-white px-3 py-1 rounded text-xs transition">
                                                    إلغاء الموعد
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal للسجل الطبي -->
    <div id="medicalRecordModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[#0B1622] rounded-2xl shadow-xl w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="bg-[#0f2538] p-6 text-white rounded-t-2xl sticky top-0 flex justify-between items-center">
                <h2 class="text-2xl font-bold">📋 السجل الطبي</h2>
                <button onclick="closeMedicalRecordModal()" class="text-white hover:text-gray-300 text-2xl">
                    &times;
                </button>
            </div>

            <!-- Modal Content -->
            <div class="p-6" id="medicalRecordContent">
                <!-- سيتم تحميل المحتوى هنا via AJAX -->
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                    <p class="text-gray-400 mt-4">جاري تحميل السجل الطبي...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript لإدارة Modal -->
    <script>
        // دالة لفتح Modal السجل الطبي
        function openMedicalRecordModal(patientId) {
            // إظهار الـ Modal
            document.getElementById('medicalRecordModal').classList.remove('hidden');

            // تحميل محتوى السجل الطبي via AJAX
            fetchMedicalRecord(patientId);
        }

        // دالة لإغلاق Modal
        function closeMedicalRecordModal() {
            document.getElementById('medicalRecordModal').classList.add('hidden');
        }

        // دالة لتحميل السجل الطبي
        function fetchMedicalRecord(patientId) {
            const contentDiv = document.getElementById('medicalRecordContent');

            // عرض مؤشر التحميل
            contentDiv.innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                    <p class="text-gray-400 mt-4">جاري تحميل السجل الطبي...</p>
                </div>
            `;

            // طلب AJAX لجلب محتوى السجل الطبي
            fetch(`/doctor/patients/${patientId}/medical-record`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(html => {
                contentDiv.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                contentDiv.innerHTML = `
                    <div class="text-center py-8 text-red-400">
                        <i class="fas fa-exclamation-triangle text-3xl mb-3"></i>
                        <p>حدث خطأ في تحميل السجل الطبي</p>
                        <button onclick="fetchMedicalRecord(${patientId})" class="mt-4 bg-blue-700 hover:bg-blue-600 text-white px-4 py-2 rounded">
                            إعادة المحاولة
                        </button>
                    </div>
                `;
            });
        }

        // إغلاق Modal عند الضغط على ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeMedicalRecordModal();
            }
        });

        // إغلاق Modal عند النقر خارج المحتوى
        document.getElementById('medicalRecordModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeMedicalRecordModal();
            }
        });
    </script>
@endsection
