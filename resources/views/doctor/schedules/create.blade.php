<x-app-layout>
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


    <div class="max-w-xl mx-auto mt-10 bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-6 text-center text-blue-700">إضافة جدول دوام جديد</h2>

        <form action=" {{ route('doctor-schedule.store') }} " method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">اليوم:</label>
                <select name="day" required
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="Monday">الاثنين</option>
                    <option value="Tuesday">الثلاثاء</option>
                    <option value="Wednesday">الأربعاء</option>
                    <option value="Thursday">الخميس</option>
                    <option value="Friday">الجمعة</option>
                    <option value="Saturday">السبت</option>
                    <option value="Sunday">الأحد</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وقت البدء:</label>
                <input type="time" name="start_time" required step="60"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">وقت النهاية:</label>
                <input type="time" name="end_time" required step="60"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <!-- عدد المرضى في الساعة -->
            <div class="mb-3">
                <label for="patients_per_hour" class="form-label">عدد المرضى في الساعة</label>
                <input type="number" name="patients_per_hour" id="patients_per_hour"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">

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
</x-app-layout>
