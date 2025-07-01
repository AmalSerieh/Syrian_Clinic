<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">تعديل الملف المهني</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded shadow">
            <form action="{{ route('doctor-profile.update', $doctorProfile->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Name (مأخوذ من المستخدم الحالي) -->
                <div class="mb-4">
                    <label class="block font-medium">الاسم</label>
                    <input type="text" value="{{ Auth::user()->name }}" disabled
                           class="w-full border-gray-300 rounded-lg shadow-sm mt-1" />
                </div>

                <!-- التخصص -->
                <div class="mb-4">
                    <label class="block font-medium">التخصص</label>
                    <input name="specialist" value="{{ $doctorProfile->specialist }}" class="w-full border-gray-300 rounded-lg mt-1" />
                </div>

                <!-- الجنس -->
                <div class="mb-4">
                    <label class="block font-medium">الجنس</label>
                    <select name="gender" class="w-full border-gray-300 rounded-lg mt-1">
                        <option value="male" {{ $doctorProfile->gender == 'male' ? 'selected' : '' }}>ذكر</option>
                        <option value="female" {{ $doctorProfile->gender == 'female' ? 'selected' : '' }}>أنثى</option>
                    </select>
                </div>

                <!-- تاريخ الميلاد -->
                <div class="mb-4">
                    <label class="block font-medium">تاريخ الميلاد</label>
                    <input type="date" name="date_birth" value="{{ $doctorProfile->date_birth }}" class="w-full border-gray-300 rounded-lg mt-1" />
                </div>

                <!-- السيرة الذاتية -->
                <div class="mb-4">
                    <label class="block font-medium">السيرة الذاتية</label>
                    <textarea name="biography" class="w-full border-gray-300 rounded-lg mt-1" rows="4">{{ $doctorProfile->biography }}</textarea>
                </div>

                <!-- معلومات الشهادة -->
                <div class="mb-4 border-t pt-4">
                    <h4 class="text-md font-semibold mb-2">بيانات الشهادة:</h4>
                    <input type="text" name="cer_name" placeholder="اسم الشهادة" value="{{ $doctorProfile->cer_name }}" class="w-full border-gray-300 rounded-lg mt-1 mb-2" />
                    <input type="text" name="cer_place" placeholder="مكان الحصول عليها" value="{{ $doctorProfile->cer_place }}" class="w-full border-gray-300 rounded-lg mt-1 mb-2" />
                    <input type="date" name="cer_date" value="{{ $doctorProfile->cer_date }}" class="w-full border-gray-300 rounded-lg mt-1 mb-2" />

                    @if ($doctorProfile->cer_images)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $doctorProfile->cer_images) }}" class="w-24 h-20 rounded-full object-cover" />
                        </div>
                    @endif
                    <input type="file" name="cer_images" class="w-full border-gray-300 rounded-lg mt-1" />
                </div>

                <!-- الخبرات -->
                <div class="mb-4 border-t pt-4">
                    <h4 class="text-md font-semibold mb-2">الخبرات:</h4>
                    <input type="text" name="exp_place" placeholder="مكان العمل" value="{{ $doctorProfile->exp_place }}" class="w-full border-gray-300 rounded-lg mt-1 mb-2" />
                    <input type="number" name="exp_years" placeholder="عدد سنوات الخبرة" value="{{ $doctorProfile->exp_years }}" class="w-full border-gray-300 rounded-lg mt-1" />
                </div>

                <!-- زر الحفظ -->
                <div class="mt-6">
                    <button type="submit"
                            class="btn-pramiry">
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
