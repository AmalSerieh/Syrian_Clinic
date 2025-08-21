@extends('layouts.secretary.header')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="p-6 flex gap-6">
        {{-- العمود الأيسر (التقويم) --}}
        <div class="w-1/2">
            {{-- اختيار المريض --}}
            <label class="block mb-2">Select Patient</label>
            <select id="patientSelect" class="border p-2 rounded w-full mb-4 bg-[#062E47]" required>
                @foreach ($patients as $patient)
                    <option class="text-black" value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                @endforeach
            </select>

            {{-- اختيار الطبيب --}}
            <label class="block mb-2">Select Doctor</label>
            <select id="doctorSelect" class="border p-2 rounded w-full mb-4 bg-[#062E47]">
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->user->name }}</option>
                @endforeach
            </select>

            {{-- التقويم --}}
            <div id="calendarWrapper" class="bg-[#062E47] p-6 rounded-xl text-white">
                <div class="flex justify-between items-center mb-4">
                    <button id="prevMonth" class="px-2 py-1 bg-gray-700 rounded hover:bg-gray-600">&larr;</button>
                    <h2 id="calendarTitle" class="text-lg font-semibold"></h2>
                    <button id="nextMonth" class="px-2 py-1 bg-gray-700 rounded hover:bg-gray-600">&rarr;</button>
                </div>

                <div class="grid grid-cols-7 gap-2 text-center text-xs text-gray-300 mb-2">
                    <span>Sun</span><span>Mon</span><span>Tue</span>
                    <span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
                </div>

                <div id="calendarDays" class="grid grid-cols-7 gap-2 text-center text-sm"></div>
            </div>
        </div>

        {{-- العمود الأيمن (الأوقات) --}}
        <div id="dayTimes" class="w-1/2 space-y-2 mt-12"></div>
    </div>

    <script>
        let selectedDoctor = null;
        let selectedPatient = null;
        let currentYear = new Date().getFullYear();
        let currentMonth = new Date().getMonth() + 1;

        const doctorSelect = document.getElementById('doctorSelect');
        const patientSelect = document.getElementById('patientSelect');
        const calendarTitle = document.getElementById('calendarTitle');
        const calendarDays = document.getElementById('calendarDays');

        // اختيار أول مريض ودكتور تلقائياً
        window.onload = function() {
            selectedDoctor = doctorSelect.value;
            selectedPatient = patientSelect.value;
            if (selectedDoctor && selectedPatient) {
                loadDays();
            }
        };

        doctorSelect.addEventListener('change', function() {
            selectedDoctor = this.value;
            if (selectedDoctor) {
                currentYear = new Date().getFullYear();
                currentMonth = new Date().getMonth() + 1;
                loadDays();
            }
        });

        document.getElementById('prevMonth').addEventListener('click', function() {
            currentMonth--;
            if (currentMonth < 1) {
                currentMonth = 12;
                currentYear--;
            }
            loadDays();
        });

        document.getElementById('nextMonth').addEventListener('click', function() {
            currentMonth++;
            if (currentMonth > 12) {
                currentMonth = 1;
                currentYear++;
            }
            loadDays();
        });

        function loadDays() {
            if (!selectedDoctor) return;


            fetch("{{ url('/api/doctor') }}/" + selectedDoctor + "/month-days?year=" + currentYear + "&month=" +
                    currentMonth, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include',
                    })

                .then(res => res.json())
                .then(data => {
                    calendarDays.innerHTML = '';

                    const firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
                    for (let i = 0; i < firstDay; i++) {
                        calendarDays.innerHTML += `<span></span>`;
                    }

                    data.data.forEach(day => {
                        const isOffDay = (day.color === '#FF0000' || day.color === 'red');

                        if (isOffDay) {
                            calendarDays.innerHTML += `
                                <div class="p-2 text-center rounded cursor-pointer bg-red-700 text-white"
                                     onclick="showNoWorkMessage('${day.date}')">
                                    ${day.date.split('-')[2]}
                                </div>`;
                        } else {
                            calendarDays.innerHTML += `
                                <div class="p-2 text-center rounded cursor-pointer "
                                     style="background:${day.color}; ${day.isfull ? 'opacity:0.5;cursor:not-allowed;color:black' : ''}"
                                     onclick="${!day.isfull ? `loadTimes('${day.date}')` : 'alert(\'اليوم ممتلئ\')'}">
                                    ${day.date.split('-')[2]}
                                </div>`;
                        }
                    });

                    const monthName = new Date(currentYear, currentMonth - 1).toLocaleString('en-US', {
                        month: 'long'
                    });
                    calendarTitle.innerText = `${monthName} ${currentYear}`;
                });
        }

        function showNoWorkMessage(date) {
            document.getElementById('dayTimes').innerHTML = `
                <div class="p-4 text-center border rounded bg-gray-100 text-gray-700 font-semibold">
                   This doctor is not available today.(${date})
                </div>`;
        }

        function loadTimes(date) {
            fetch("{{ url('/api/doctor') }}/" + selectedDoctor + "/day-slots?date=" + date, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'include'
                }).then(res => res.json())
                .then(data => {
                    let timesHTML = '';

                    if (data.data.length === 0) {
                        timesHTML = `
                            <div class="p-4 text-center border rounded bg-yellow-100 text-yellow-600 font-semibold">
                                There are no appointments available today.
                            </div>`;
                    } else {
                        data.data.forEach(slot => {
                            if (!slot.time) return;
                            timesHTML += `
                                <div class="flex justify-between items-center p-2 border rounded">
                                    <span>${slot.time}</span>
                                    ${slot.isfull
                                        ? '<span class="text-red-500">FULL</span>'
                                        : `<button class="bg-green-600 text-white px-3 py-1 rounded"
                                                                   onclick="showBookingDialog('${date}','${slot.time}')">BOOK NOW</button>`}
                                </div>`;
                        });
                    }

                    document.getElementById('dayTimes').innerHTML = timesHTML;
                });
        }

        function showBookingDialog(date, time) {
            selectedPatient = patientSelect.value;
            if (!selectedPatient) return alert('اختر المريض أولاً');

            const dialog = document.createElement('div');
            dialog.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            dialog.innerHTML = `
                <div class="bg-[#062E47] text-white p-6 rounded-lg w-full max-w-md">
                    <h3 class="text-xl font-bold mb-4">Complete Booking</h3>
                    <form id="bookingForm" class="space-y-4">
                        <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                        <input type="hidden" name="doctor_id" value="${selectedDoctor}">
                        <input type="hidden" name="patient_id" value="${selectedPatient}">
                        <input type="hidden" name="date" value="${date}">
                        <input type="hidden" name="start_time" value="${time.split('-')[0]}">
                        <input type="hidden" name="end_time" value="${time.split('-')[1]}">

                        <div class="bg-gray-100 bg-opacity-20 p-3 rounded-xl text-blue-300 text-sm">
                            <p><strong>Date:</strong> ${date}</p>
                            <p><strong>Time Start:</strong> ${time.split('-')[0]}</p>
                            <p><strong>Time End:</strong> ${time.split('-')[1]}</p>
                        </div>

                        <div>
                            <label class="block mb-1">Type of Visit</label>
                            <select name="type_visit" class="border p-2 rounded w-full text-black" required>
                                <option value="appointment">appointment</option>
                                <option value="review">review</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1">Location Type</label>
                            <select name="location_type" class="border p-2 rounded w-full text-black" required>
                                <option value="in_Clinic">in_Clinic</option>
                                <option value="in_Home">in_Home</option>
                                <option value="on_Street">on_Street</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1">Arrival time (minutes)</label>
                            <input type="number" name="arrivved_time" class="border p-2 rounded w-full text-black" min="1" value="30" required>
                        </div>

                        <div class="flex justify-end gap-2 pt-4">
                            <button type="button" onclick="this.closest('div[class^=fixed]').remove()"
                                    class="px-4 py-2 bg-gray-300 rounded text-black">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Save</button>
                        </div>
                    </form>
                </div>`;

            document.body.appendChild(dialog);

            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                e.preventDefault();
                bookAppointment(this);
            });
        }

        async function bookAppointment(form) {
            const formData = new FormData(form);

            try {
                // أولاً: تجديد الجلسة


                const response = await fetch("{{ route('secretary.patient.book.store') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },

                    body: formData,
                    credentials: 'include'

                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Server error');
                }

                const result = await response.json();
                alert(result.message || 'تم حجز الموعد بنجاح');
                document.querySelector('div[class^=fixed]').remove();
                loadTimes(formData.get('date'));
                window.location.href = "{{ route('secretary.appointments') }}";

            } catch (error) {
                console.error('Error:', error);
                alert(error.message || 'حدث خطأ أثناء الحجز');
            }
        }
    </script>
@endsection
