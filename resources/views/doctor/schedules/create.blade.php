@extends('layouts.doctor.header')

@section('content')
    @if (session(key: 'info'))
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


    <div class="max-w-xl mx-auto mt-10  shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-6 text-center text-blue-700">إضافة جدول دوام جديد</h2>

        <form action=" {{ route('doctor-schedule.store') }} " method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">اليوم:</label>
                <select name="day" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-black">
                    <option value="Monday" class="text-black">Monday</option>
                    <option value="Tuesday"class="text-black">Tuesday</option>
                    <option value="Wednesday"class="text-black">Wednesday</option>
                    <option value="Thursday"class="text-black">Thursday</option>
                    <option value="Friday"class="text-black">Friday</option>
                    <option value="Saturday"class="text-black">Saturday</option>
                    <option value="Sunday"class="text-black">Sunday</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1 text-black">وقت البدء:</label>
                <input type="time" name="start_time" required step="60"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-black">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">وقت النهاية:</label>
                <input type="time" name="end_time" required step="60"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-black">
            </div>
            <!-- عدد المرضى في الساعة -->
            <div class="mb-3">
                <label for="patients_per_hour" class="form-label">عدد المرضى في الساعة</label>
                <input type="number" min=1 name="patients_per_hour" id="patients_per_hour"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-black">

                @error('patients_per_hour')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                <small class="text-muted">أدخل عدد المرضى الذين يمكنك استقبالهم في الساعة.</small>
            </div>
            {{--     <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">عدد المرضى:</label>
                <input type="number" name="max_patients" required min="1"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">مدة الموعد (بالدقائق):</label>
                <input type="number" name="appointment_duration" required min="5" step="5"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div> --}}

            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-red-600 text-black py-2 px-4 rounded hover:bg-blue-700 transition">
                    حفظ الموعد
                </button>
            </div>
        </form>
    </div>
@endsection
