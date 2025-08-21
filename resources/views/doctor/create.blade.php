@extends('layouts.doctor.header')

@section('content')
    @if ($errors->any())
        <div class="mb-6 rounded bg-red-100 border border-red-300 text-red-800 px-4 py-3">
            <ul class="list-disc pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-3xl mx-auto py-10">
        <h2 class="text-2xl sm:text-3xl font-bold mb-8 text-center text-white bg-[#062E47] rounded-xl shadow py-5 tracking-wide">إدخال الملف المهني للطبيب</h2>

        @if (session('info'))
            <div class="mb-6 rounded bg-blue-100 text-blue-900 p-3 text-center shadow">{{ session('info') }}</div>
        @endif

        <form action="{{ route('doctor-profile.store') }}" method="POST" enctype="multipart/form-data"
              class="bg-[#062E47] rounded-2xl shadow-xl p-8 space-y-8 border-2 border-[#164C77]">
            @csrf

            {{-- المعلومات الأساسية --}}
            <div>
                <h3 class="text-lg font-bold text-[#E0EFFE] mb-5 border-b border-[#164C77] pb-2">المعلومات الأساسية</h3>
                <div class="mb-4">
                    <x-input-label for="biography" :value="'السيرة الذاتية'" class="text-[#B6D1EB]" />
                    <textarea id="biography" name="biography" rows="4"
                              class="mt-1 block w-full border-gray-400 bg-[#f8fafc] text-[#062E47] rounded shadow-sm focus:ring-[#164C77] focus:border-[#164C77]"
                              placeholder="اكتب نبذة مختصرة...">{{ old('biography') }}</textarea>
                    <x-input-error :messages="$errors->get('biography')" class="mt-2" />
                </div>
                <div class="mb-4">
                    <x-input-label for="date_birth" :value="'تاريخ الميلاد'" class="text-[#B6D1EB]" />
                    <x-text-input id="date_birth" name="date_birth" type="date"
                        class="mt-1 block w-full border-gray-400 bg-[#f8fafc] text-[#062E47] rounded"
                        :value="old('date_birth')" required />
                    <x-input-error :messages="$errors->get('date_birth')" class="mt-2" />
                </div>
            </div>

            {{-- معلومات الشهادة --}}
            <div>
                <h3 class="text-lg font-bold text-[#E0EFFE] mb-5 border-b border-[#164C77] pb-2">معلومات الشهادة</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="cer_name" :value="'اسم الشهادة'" class="text-[#B6D1EB]" />
                        <x-text-input id="cer_name" name="cer_name" type="text"
                            class="mt-1 block w-full border-gray-400 bg-[#f8fafc] text-[#062E47] rounded" :value="old('cer_name')" />
                        <x-input-error :messages="$errors->get('cer_name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="cer_place" :value="'جهة الإصدار'" class="text-[#B6D1EB]" />
                        <x-text-input id="cer_place" name="cer_place" type="text"
                            class="mt-1 block w-full border-gray-400 bg-[#f8fafc] text-[#062E47] rounded" :value="old('cer_place')" />
                        <x-input-error :messages="$errors->get('cer_place')" class="mt-2" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                    <div>
                        <x-input-label for="cer_date" :value="'تاريخ الشهادة'" class="text-[#B6D1EB]" />
                        <x-text-input id="cer_date" name="cer_date" type="date"
                            class="mt-1 block w-full border-gray-400 bg-[#f8fafc] text-[#062E47] rounded" :value="old('cer_date')" />
                        <x-input-error :messages="$errors->get('cer_date')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="cer_images" :value="'صورة الشهادة'" class="text-[#B6D1EB]" />
                        <input id="cer_images" name="cer_images" type="file"
                            class="mt-1 block w-full border border-gray-400 bg-white text-sm rounded-lg cursor-pointer focus:outline-none" />
                        <x-input-error :messages="$errors->get('cer_images')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- معلومات الخبرة --}}
            <div>
                <h3 class="text-lg font-bold text-[#E0EFFE] mb-5 border-b border-[#164C77] pb-2">معلومات الخبرة</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="exp_place" :value="'مكان الخبرة'" class="text-[#B6D1EB]" />
                        <x-text-input id="exp_place" name="exp_place" type="text"
                            class="mt-1 block w-full border-gray-400 bg-[#f8fafc] text-[#062E47] rounded" :value="old('exp_place')" />
                        <x-input-error :messages="$errors->get('exp_place')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="exp_years" :value="'عدد سنوات الخبرة'" class="text-[#B6D1EB]" />
                        <x-text-input id="exp_years" name="exp_years" type="number" min=0
                            class="mt-1 block w-full border-gray-400 bg-[#f8fafc] text-[#062E47] rounded"
                            :value="old('exp_years')" />
                        <x-input-error :messages="$errors->get('exp_years')" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit"
                    class="w-full py-3 bg-gradient-to-r from-[#164C77] to-[#062E47] text-white text-lg font-semibold rounded-2xl shadow hover:bg-[#04162c] transition">
                    ➕ حفظ الملف المهني
                </button>
            </div>
        </form>
    </div>
@endsection
