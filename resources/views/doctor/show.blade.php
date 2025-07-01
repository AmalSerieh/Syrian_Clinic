<x-app-layout>

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded shadow p-6">

    {{-- بيانات الطبيب --}}
    <div class="flex items-center space-x-4 mb-6">
        <img src="{{ asset('storage/' . $doctor->photo) }}" alt="صورة الطبيب" class="w-24 h-24 rounded-full border object-cover">
        <div>
            <h2 class="text-2xl font-bold">{{ $doctor->user->name }}</h2>
            <p class="text-gray-600">الاختصاص: {{ $profile->specialist }}</p>
            <p class="text-gray-500">سنوات الخبرة: {{ $profile->exp_years }} سنة</p>
            <p class="text-gray-500">السيرة الذاتية: {{ $profile->biography }}</p>
        </div>
    </div>

    {{-- جدول المواعيد الأصلية --}}
    <h3 class="text-xl font-semibold mb-3">المواعيد المتاحة:</h3>
    <table class="w-full text-center mb-6 border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">اليوم</th>
                <th class="border px-4 py-2">من</th>
                <th class="border px-4 py-2">إلى</th>
                <th class="border px-4 py-2">مدة الموعد</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedules as $schedule)
                <tr>
                    <td class="border px-4 py-2">{{ $schedule->day }}</td>
                    <td class="border px-4 py-2">{{ $schedule->start_time }}</td>
                    <td class="border px-4 py-2">{{ $schedule->end_time }}</td>
                    <td class="border px-4 py-2">{{ $schedule->appointment_duration }} دقيقة</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- عرض الفترات المجزأة --}}
    <h3 class="text-xl font-semibold mb-3">الفترات الزمنية المتاحة:</h3>
    <table class="w-full text-center border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">اليوم</th>
                <th class="border px-4 py-2">من</th>
                <th class="border px-4 py-2">إلى</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timeRanges as $range)
                <tr>
                    <td class="border px-4 py-2">{{ $range['day'] }}</td>
                    <td class="border px-4 py-2">{{ $range['from'] }}</td>
                    <td class="border px-4 py-2">{{ $range['to'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</x-app-layout>
