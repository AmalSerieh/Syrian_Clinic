@extends('layouts.secretary.header')

@section('content')
<div class="p-6 space-y-6">

    {{-- اختيار المريض والطبيب --}}
    <div class="bg-white p-4 rounded-xl shadow">
        <label class="block mb-2">المريض</label>
        <select id="patientSelect" class="border p-2 rounded w-full mb-4" required>
            <option value="">اختر المريض</option>
            @foreach ($patients as $patient)
                <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
            @endforeach
        </select>

        <label class="block mb-2">الطبيب</label>
        <select id="doctorSelect" class="border p-2 rounded w-full">
            <option value="">اختر الطبيب</option>
            @foreach ($doctors as $doctor)
                <option value="{{ $doctor->id }}">{{ $doctor->user->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- التقويم --}}
    <div id="calendarWrapper" class="bg-[#062E47] p-6 rounded-xl text-white hidden">
        <div class="flex justify-between items-center mb-4">
            <button id="prevMonth" class="px-2 py-1 bg-gray-700 rounded hover:bg-gray-600">&larr;</button>
            <h2 id="calendarTitle" class="text-lg font-semibold"></h2>
            <button id="nextMonth" class="px-2 py-1 bg-gray-700 rounded hover:bg-gray-600">&rarr;</button>
        </div>

        <div class="grid grid-cols-7 gap-2 text-center text-xs text-gray-300 mb-2">
            <span>Sun</span><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span>
        </div>

        <div id="calendarDays" class="grid grid-cols-7 gap-2 text-center text-sm"></div>
    </div>

    {{-- الأوقات --}}
    <div id="dayTimes" class="space-y-2"></div>

</div>

<script>
    let selectedDoctor = null;
    let selectedPatient = null;
    let currentDate = new Date();

    document.getElementById('doctorSelect').addEventListener('change', function () {
        selectedDoctor = this.value;
        if (!selectedDoctor) return;
        document.getElementById('calendarWrapper').classList.remove('hidden');
        loadMonth();
    });

    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        loadMonth();
    });
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        loadMonth();
    });

    function loadMonth() {
        let year = currentDate.getFullYear();
        let month = currentDate.getMonth() + 1;

        fetch(`/doctor/${selectedDoctor}/month-days?year=${year}&month=${month}`)
            .then(res => res.json())
            .then(data => {
                let daysHTML = '';
                let firstDay = new Date(year, month - 1, 1).getDay();
                let daysInMonth = new Date(year, month, 0).getDate();

                // تحديث العنوان
                document.getElementById('calendarTitle').textContent =
                    currentDate.toLocaleString('default', { month: 'long', year: 'numeric' });

                // فراغات قبل أول يوم
                for (let i = 0; i < firstDay; i++) {
                    daysHTML += `<span></span>`;
                }

                for (let d = 1; d <= daysInMonth; d++) {
                    let fullDate = `${year}-${String(month).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                    let dayInfo = data.data.find(x => x.date === fullDate);
                    let bg = dayInfo ? dayInfo.color : '#374151';
                    let disabled = dayInfo && dayInfo.isfull ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
                    daysHTML += `
                        <div class="p-2 rounded text-white ${disabled}"
                             style="background:${bg}"
                             onclick="${dayInfo && !dayInfo.isfull ? `loadTimes('${fullDate}')` : ''}">
                            ${d}
                        </div>`;
                }
                document.getElementById('calendarDays').innerHTML = daysHTML;
            });
    }

    function loadTimes(date) {
        fetch(`/doctor/${selectedDoctor}/day-slots?date=${date}`)
            .then(res => res.json())
            .then(data => {
                let timesHTML = `<h3 class="font-bold mb-2">الأوقات المتاحة (${date})</h3>`;
                data.data.forEach(slot => {
                    if (!slot.time) return;
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
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                patient_id: selectedPatient,
                doctor_id: selectedDoctor,
                date: date,
                time: time
            })
        }).then(res => res.json())
          .then(resp => {
            alert(resp.status || resp.error);
            if (resp.status) loadTimes(date);
        });
    }
</script>
@endsection
