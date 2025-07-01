<x-app-layout>
    {{-- <div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
        <h2 class="text-xl font-bold mb-6">إضافة طبيب جديد</h2>

        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ session('error') }}</div>
        @endif

        <form action="{{ route('admin.doctors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- بيانات المستخدم -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label>الاسم</label>
                    <input type="text" name="name" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label>الإيميل</label>
                    <input type="email" name="email" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label>رقم الهاتف</label>
                    <input type="text" name="phone" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label>كلمة السر</label>
                    <input type="password" name="password" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label>تأكيد كلمة السر</label>
                    <input type="password" name="password_confirmation" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label>صورة الطبيب (اختياري)</label>
                    <input type="file" name="photo" class="w-full">
                </div>
            </div>

            <!-- بيانات الملف المهني -->
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label>الاختصاص</label>
                    <input type="text" name="specialist" class="w-full border rounded p-2" required>
                </div>
                <div>
                    <label>مكان الحصول على الشهادة</label>
                    <input type="text" name="cer_place" class="w-full border rounded p-2">
                </div>
                <div>
                    <label>اسم الشهادة</label>
                    <input type="text" name="cer_name" class="w-full border rounded p-2">
                </div>
                <div>
                    <label>صورة الشهادة</label>
                    <input type="file" name="cer_images" class="w-full">
                </div>
                <div>
                    <label>تاريخ الشهادة</label>
                    <input type="date" name="cer_date" class="w-full border rounded p-2">
                </div>
                <div>
                    <label>مكان الخبرة</label>
                    <input type="text" name="exp_place" class="w-full border rounded p-2">
                </div>
                <div>
                    <label>سنوات الخبرة</label>
                    <input type="number" name="exp_years" class="w-full border rounded p-2">
                </div>
                <div>
                    <label>الجنس</label>
                    <select name="gender" class="w-full border rounded p-2">
                        <option value="ذكر">ذكر</option>
                        <option value="أنثى">أنثى</option>
                    </select>
                </div>
                <div>
                    <label>تاريخ الميلاد</label>
                    <input type="date" name="date_birth" class="w-full border rounded p-2">
                </div>
                <div class="col-span-2">
                    <label>السيرة الذاتية</label>
                    <textarea name="biography" rows="3" class="w-full border rounded p-2"></textarea>
                </div>
            </div>

            <!-- اختيار الغرفة -->
            <div class="mb-4">
                <label for="room_id">اختر الغرفة:</label>
                <select name="room_id" class="w-full border rounded p-2" required>
                    <option value="">-- اختر غرفة --</option>
                    @foreach ($rooms as $room)
                        @php
                            $doctorCount = \App\Models\Doctor::where('room_id', $room->id)->count();
                        @endphp
                        @if ($doctorCount < $room->capacity)
                            <option value="{{ $room->id }}">
                                {{ $room->name }} ({{ $doctorCount }}/{{ $room->capacity }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">إضافة الطبيب</button>
        </form>
    </div> --}}
    <select name="room_id" required class="w-full border rounded p-2">
    <option value="">اختر الغرفة</option>
    @foreach ($rooms as $room)
        <option value="{{ $room['id'] }}">
            {{ $room['name'] }} - {{ $room['specialty'] }}
        </option>
    @endforeach
</select>

</x-app-layout>
