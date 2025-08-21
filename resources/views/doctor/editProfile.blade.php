@extends('layouts.doctor.header')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white bg-[#062E47] p-4 rounded-xl shadow mb-8 text-center">
            تعديل الملف المهني
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto bg-[#062E47] p-8 rounded-2xl shadow-lg border-2 border-[#164C77]">
            <form action="{{ route('doctor-profile.update', $doctorProfile->id) }}" method="POST"
                enctype="multipart/form-data" class="space-y-7">
                @csrf
                @method('PUT')

                <!-- اسم الطبيب (مقروء فقط) -->
                <div>
                    <label class="block font-semibold text-[#B6D1EB] mb-2">الاسم</label>
                    <input type="text" value="{{ Auth::user()->name }}" disabled
                        class="w-full bg-[#F8FAFC] border-gray-300 rounded-xl shadow-sm py-2 mt-1 text-[#062E47]" />
                </div>

                <!-- تاريخ الميلاد -->
                <div>
                    <label for="date_birth" class="block font-semibold text-[#B6D1EB] mb-2">تاريخ الميلاد</label>
                    <x-text-input id="date_birth" name="date_birth" type="date"
                        class="mt-1 block w-full border-gray-400 bg-[#f8fafc] text-[#062E47] rounded-xl"
                        :value="old('date_birth', $doctorProfile->date_birth)" required />
                    <x-input-error :messages="$errors->get('date_birth')" class="mt-2" />
                </div>

                <!-- السيرة الذاتية -->
                <div>
                    <label class="block font-semibold text-[#B6D1EB] mb-2">السيرة الذاتية</label>
                    <textarea name="biography"
                        class="w-full bg-[#F8FAFC] border-gray-300 rounded-xl mt-1 py-2 text-[#062E47] shadow-sm"
                        rows="4">{{ $doctorProfile->biography }}</textarea>
                    <x-input-error :messages="$errors->get('biography')" class="mt-2" />
                </div>

                <!-- معلومات الشهادة -->
                <div class="border-t border-[#164C77] pt-4">
                    <h4 class="text-md font-bold text-[#E0EFFE] mb-3">بيانات الشهادة</h4>
                    <input type="text" name="cer_name" placeholder="اسم الشهادة"
                        value="{{ $doctorProfile->cer_name }}" class="w-full bg-[#f8fafc] border-gray-400 rounded-xl py-2 mt-1 mb-2 text-[#062E47]" />
                    <input type="text" name="cer_place" placeholder="مكان الحصول عليها"
                        value="{{ $doctorProfile->cer_place }}" class="w-full bg-[#f8fafc] border-gray-400 rounded-xl py-2 mt-1 mb-2 text-[#062E47]" />
                    <input type="date" name="cer_date" value="{{ $doctorProfile->cer_date }}"
                        class="w-full bg-[#f8fafc] border-gray-400 rounded-xl py-2 mt-1 mb-2 text-[#062E47]" />

                    @if ($doctorProfile->cer_images)
                        <div class="mb-2 flex items-center">
                            <img src="{{ asset('storage/' . $doctorProfile->cer_images) }}"
                                class="w-24 h-20 rounded-full object-cover border-2 border-[#164C77] mr-3" />
                            <span class="text-white text-sm">الصورة الحالية</span>
                        </div>
                    @endif

                    <input type="file" name="cer_images" class="w-full border border-gray-400 rounded-xl bg-white mt-1 py-2" />
                    <x-input-error :messages="$errors->get('cer_images')" class="mt-2" />
                </div>

                <!-- الخبرات -->
                <div class="border-t border-[#164C77] pt-4">
                    <h4 class="text-md font-bold text-[#E0EFFE] mb-3">الخبرات</h4>
                    <input type="text" name="exp_place" placeholder="مكان العمل"
                        value="{{ $doctorProfile->exp_place }}" class="w-full border-gray-400 bg-[#f8fafc] rounded-xl py-2 mt-1 mb-2 text-[#062E47]" />
                    <input type="number" name="exp_years" placeholder="عدد سنوات الخبرة"
                        value="{{ $doctorProfile->exp_years }}" class="w-full border-gray-400 bg-[#f8fafc] rounded-xl py-2 mt-1 text-[#062E47]" />
                    <x-input-error :messages="$errors->get('exp_years')" class="mt-2" />
                </div>

                <!-- زر الحفظ -->
                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-3 bg-gradient-to-r from-[#164C77] to-[#062E47] text-white text-lg font-semibold rounded-2xl shadow hover:bg-[#04162c] transition">
                        💾 حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
