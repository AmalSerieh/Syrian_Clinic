@extends('layouts.doctor.header')

@section('content')    <div class="max-w-xl mx-auto mt-10 bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-6 text-center text-blue-700">تعديل جدول دوام</h2>

        @if (session('info'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                {{ session('info') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('doctor-schedule.update', $schedule->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <!-- يوم الأسبوع -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">اليوم:</label>
                <select name="day" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach([
                        'Saturday' => 'السبت',
                        'Sunday' => 'الأحد',
                        'Monday' => 'الإثنين',
                        'Tuesday' => 'الثلاثاء',
                        'Wednesday' => 'الأربعاء',
                        'Thursday' => 'الخميس',
                        'Friday' => 'الجمعة'
                    ] as $value => $label)
                        <option value="{{ $value }}" {{ $schedule->day == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- وقت البدء -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وقت البدء:</label>
                <input type="time" name="start_time"
                       value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"
                       required step="900"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- وقت النهاية -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وقت النهاية:</label>
                <input type="time" name="end_time"
                       value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"
                       required step="900"
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- عدد المرضى في الساعة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">عدد المرضى في الساعة:</label>
                <input type="number" name="patients_per_hour"
                       value="{{ $schedule->appointment_duration ? round(60 / $schedule->appointment_duration) : 4 }}"
                       min="1" max="60" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">سيتم حساب مدة الموعد تلقائياً (60 / عدد المرضى)</p>
            </div>

            <!-- معاينة الحسابات -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-2">معاينة الحسابات:</h3>
                <div class="grid grid-cols-2 gap-2">
                    <p class="text-sm">مدة الموعد:</p>
                    <p class="text-sm font-semibold">
                        @if($schedule->appointment_duration)
                            {{ round($schedule->appointment_duration) }} دقيقة
                        @else
                            سيتم الحساب بعد الحفظ
                        @endif
                    </p>

                    <p class="text-sm">الحد الأقصى للمرضى:</p>
                    <p class="text-sm font-semibold">
                        @php
                            $start = \Carbon\Carbon::parse($schedule->start_time);
                            $end = \Carbon\Carbon::parse($schedule->end_time);
                            $hours = $end->diffInMinutes($start) / 60;
                            $max_patients = round($hours * ($schedule->appointment_duration ? (60 / $schedule->appointment_duration) : 4));
                        @endphp
                        {{ $max_patients }} مريض
                    </p>
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                حفظ التعديلات
            </button>
        </form>
    </div>
@endsection
