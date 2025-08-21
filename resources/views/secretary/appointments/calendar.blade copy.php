@extends('layouts.secretary.header')

@section('content')
<div class="p-6">
    <label class="block mb-2">المريض</label>
    <select id="patientSelect" class="border p-2 rounded w-full mb-4" required>
        <option class="text-black" value="">اختر المريض</option>
        @foreach ($patients as $patient)
            <option class="text-black" value="{{ $patient->id }}">{{ $patient->user->name }}</option>
        @endforeach
    </select>

    <label class="block mb-2">الطبيب</label>
    <select id="doctorSelect" class="border p-2 rounded w-full mb-4">
        <option value="">اختر الطبيب</option>
        @foreach ($doctors as $doctor)
            <option value="{{ $doctor->id }}">{{ $doctor->user->name }}</option>
        @endforeach
    </select>

    <div id="calendarDays" class="grid grid-cols-4 gap-2 mb-4"></div>
    <div id="dayTimes" class="space-y-2"></div>
</div>

<script>
let selectedDoctor = null;
let selectedPatient = null;
let currentYear = new Date().getFullYear();
let currentMonth = new Date().getMonth() + 1; // 1-based

document.getElementById('doctorSelect').addEventListener('change', function () {
    selectedDoctor = this.value;
    currentYear = new Date().getFullYear();
    currentMonth = new Date().getMonth() + 1;
    loadDays();
});

function loadDays() {
    if (!selectedDoctor) return;
    fetch(`/api/doctor/${selectedDoctor}/month-days?year=${currentYear}&month=${currentMonth}`)
        .then(res => res.json())
        .then(data => {
            let daysHTML = '';
            if (data.data.length === 0) {
                // إذا ما فيه أيام، انتقل للشهر التالي
                currentMonth++;
                if (currentMonth > 12) {
                    currentMonth = 1;
                    currentYear++;
                }
                loadDays();
                return;
            }
            data.data.forEach(day => {
                daysHTML += `
                    <div class="p-2 text-center rounded text-white cursor-pointer"
                         style="background:${day.color}"
                         onclick="loadTimes('${day.date}', ${day.isfull})">
                        ${day.day_name}<br>${day.date}
                    </div>`;
            });
            document.getElementById('calendarDays').innerHTML = daysHTML;
        });
}

function loadTimes(date, isFull) {
    if (isFull) return alert('اليوم ممتلئ');
    fetch(`/api/doctor/${selectedDoctor}/day-slots?date=${date}`)
        .then(res => res.json())
        .then(data => {
            let timesHTML = '';
            data.data.forEach(slot => {
                timesHTML += `
                    <div class="flex justify-between items-center p-2 border rounded">
                        <span>${slot.time}</span>
                        ${slot.isfull
                            ? '<span class="text-red-500">ممتلئ</span>'
                            : `<button class="bg-green-500 text-white px-3 py-1 rounded"
                                       onclick="book('${date}','${slot.time}')">حجز</button>`}
                    </div>`;
            });
            document.getElementById('dayTimes').innerHTML = timesHTML;
        });
}

function book(date, time) {
    selectedPatient = document.getElementById('patientSelect').value;
    if (!selectedPatient) return alert('اختر المريض أولاً');

    fetch('/secretary/appointments/book', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify({
            patient_id: selectedPatient,
            doctor_id: selectedDoctor,
            date: date,
            time: time
        })
    }).then(res => res.json())
      .then(resp => {
        alert(resp.status || resp.error);
        if (resp.status) loadTimes(date, false);
      });
}
</script>
@endsection
