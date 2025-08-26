@extends('layouts.doctor.header')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('الملف المهني') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-500 to-indigo-100 min-h-screen" x-data="{ showEditModal: false }">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @php
                $doctor = Auth::user()->doctor;
                //dd($doctor, $doctor->doctorProfile);
            @endphp

            @if ($doctor)
                <!-- Header Section -->
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 bg-white px-6 py-3 rounded-2xl shadow-lg">
                        الملف المهني للطبيب
                    </h1>
                    <button @click="showEditModal = true"
                        class="inline-flex items-center bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-medium px-6 py-3 rounded-xl shadow-lg hover:from-blue-700 hover:to-indigo-800 transition-all duration-300 transform hover:-translate-y-1">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        تعديل الملف المهني
                    </button>
                </div>



                <!-- Main Profile Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <!-- Profile Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                        <div class="flex flex-col md:flex-row items-center gap-6">
                            <div class="relative">
                                <img src="{{ asset('storage/' . $doctor->photo) }}" alt="Doctor"
                                    class="w-32 h-32 object-cover rounded-full border-4 border-white shadow-2xl">
                                <div class="absolute -bottom-2 -right-2 bg-green-500 rounded-full p-2 shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-center md:text-right">
                                <h2 class="text-3xl font-bold mb-2">{{ Auth::user()->name }}</h2>
                                <p class="text-blue-100 text-lg">{{ $doctor->doctorProfile->specialist_ar }}</p>
                                <p class="text-blue-200 mt-1">{{ $doctor->doctorProfile->specialist_en }}</p>
                            </div>
                        </div>
                    </div>


                    <!-- Profile Content -->
                    <div class="p-6">
                        <!-- Personal Information Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                            <!-- Gender Card -->
                            <div class="bg-blue-50 rounded-xl p-4 border-l-4 border-blue-400">
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">الجنس</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ $doctor->doctorProfile->gender == 'male' ? 'ذكر' : 'أنثى' }}</p>
                                    </div>
                                </div>
                            </div>


                            <!-- Birth Date Card -->
                            <div class="bg-purple-50 rounded-xl p-4 border-l-4 border-purple-400">
                                <div class="flex items-center gap-3">
                                    <div class="bg-purple-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">تاريخ الميلاد</p>
                                        <p class="font-semibold text-gray-800">
                                            {{ \Carbon\Carbon::parse($doctor->doctorProfile->date_birth)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>


                            <!-- Experience Years Card -->
                            <div class="bg-green-50 rounded-xl p-4 border-l-4 border-green-400">
                                <div class="flex items-center gap-3">
                                    <div class="bg-green-100 p-3 rounded-full">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">سنوات الخبرة</p>
                                        <p class="font-semibold text-gray-800">{{ $doctor->doctorProfile->exp_years }} سنة
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- Biography Section -->
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                السيرة الذاتية
                            </h3>
                            <div class="bg-gray-50 rounded-xl p-6 border-2 border-dashed border-gray-200">
                                <p class="text-gray-700 leading-relaxed text-justify">
                                    {{ $doctor->doctorProfile->biography }}
                                </p>
                            </div>
                        </div>


                        <!-- Certificates Section -->
                        <div class="mb-8">
                            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                                الشهادات العلمية
                            </h3>
                            <div class="bg-blue-50 rounded-xl p-6 border-2 border-blue-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-sm text-blue-600 mb-1">اسم الشهادة</p>
                                        <p class="font-semibold text-gray-800">{{ $doctor->doctorProfile->cer_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-blue-600 mb-1">مكان الحصول</p>
                                        <p class="font-semibold text-gray-800">{{ $doctor->doctorProfile->cer_place }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-blue-600 mb-1">تاريخ الشهادة</p>
                                        <p class="font-semibold text-gray-800">{{ $doctor->doctorProfile->cer_date }}</p>
                                    </div>
                                    @if ($doctor->doctorProfile->cer_images)
                                        <div class="md:col-span-2">
                                            <p class="text-sm text-blue-600 mb-2">صورة الشهادة</p>
                                            <img src="{{ asset('storage/' . $doctor->doctorProfile->cer_images) }}"
                                                class="w-full max-w-md rounded-lg shadow-lg border-2 border-white mx-auto"
                                                alt="صورة الشهادة">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>


                        <!-- Experience Section -->
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                                الخبرة العملية
                            </h3>
                            <div class="bg-green-50 rounded-xl p-6 border-2 border-green-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-sm text-green-600 mb-1">مكان العمل</p>
                                        <p class="font-semibold text-gray-800">{{ $doctor->doctorProfile->exp_place }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-green-600 mb-1">مدة الخبرة</p>
                                        <p class="font-semibold text-gray-800">{{ $doctor->doctorProfile->exp_years }} سنة
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
                    style="display: none;">
                    <div @click.outside="showEditModal = false"
                        class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                        <!-- Modal Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white rounded-t-2xl">
                            <div class="flex justify-between items-center">
                                <h2 class="text-2xl font-bold">تعديل الملف المهني</h2>
                                <button @click="showEditModal = false" class="text-white hover:text-gray-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Content -->
                        <div class="p-6">
                            <form action="{{ route('doctor-profile.update', $doctor->doctorProfile->id) }}"
                                method="POST" enctype="multipart/form-data" class="space-y-6">
                                @csrf
                               {{--  @method('PUT') --}}

                                <!-- اسم الطبيب (مقروء فقط) -->
                                <div>
                                    <label class="block font-semibold text-gray-700 mb-2">الاسم</label>
                                    <input type="text" value="{{ Auth::user()->name }}" disabled
                                        class="w-full bg-gray-100 border-gray-300 rounded-xl shadow-sm py-2 px-4 mt-1 text-gray-600" />
                                </div>

                                <!-- تاريخ الميلاد -->
                                <div>
                                    <label for="date_birth" class="block font-semibold text-gray-700 mb-2">تاريخ
                                        الميلاد</label>
                                    <input id="date_birth" name="date_birth" type="date"
                                        class="w-full border-gray-300 bg-white rounded-xl py-2 px-4 mt-1 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                        value="{{ old('date_birth', optional($doctor->doctorProfile)->date_birth) }}"
                                        required />

                                    @error('date_birth')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- السيرة الذاتية -->
                                <div>
                                    <label class="block font-semibold text-gray-700 mb-2">السيرة الذاتية</label>
                                    <textarea name="biography"
                                        class="w-full bg-white border-gray-300 rounded-xl mt-1 py-2 px-4 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                        rows="4">{{ old('biography', $doctor->doctorProfile->biography) }}</textarea>
                                    @error('biography')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- معلومات الشهادة -->
                                <div class="border-t border-gray-200 pt-4">
                                    <h4 class="text-lg font-bold text-gray-800 mb-3">بيانات الشهادة</h4>
                                    <input type="text" name="cer_name" placeholder="اسم الشهادة"
                                        value="{{ old('cer_name', $doctor->doctorProfile->cer_name) }}"
                                        class="w-full bg-white border-gray-300 rounded-xl py-2 px-4 mt-1 mb-2 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" />

                                    <input type="text" name="cer_place" placeholder="مكان الحصول عليها"
                                        value="{{ old('cer_place', $doctor->doctorProfile->cer_place) }}"
                                        class="w-full bg-white border-gray-300 rounded-xl py-2 px-4 mt-1 mb-2 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" />

                                    <input type="date" name="cer_date"
                                        value="{{ old('cer_date', $doctor->doctorProfile->cer_date) }}"
                                        class="w-full bg-white border-gray-300 rounded-xl py-2 px-4 mt-1 mb-2 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" />

                                    @if ($doctor->doctorProfile->cer_images)
                                        <div class="mb-3 flex items-center">
                                            <img src="{{ asset('storage/' . $doctor->doctorProfile->cer_images) }}"
                                                class="w-24 h-20 rounded-lg object-cover border-2 border-gray-300 mr-3" />
                                            <span class="text-gray-600 text-sm">الصورة الحالية</span>
                                        </div>
                                    @endif

                                    <input type="file" name="cer_images"
                                        class="w-full border border-gray-300 rounded-xl bg-white mt-1 py-2 px-4 text-gray-700" />
                                    @error('cer_images')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- الخبرات -->
                                <div class="border-t border-gray-200 pt-4">
                                    <h4 class="text-lg font-bold text-gray-800 mb-3">الخبرات</h4>
                                    <input type="text" name="exp_place" placeholder="مكان العمل"
                                        value="{{ old('exp_place', $doctor->doctorProfile->exp_place) }}"
                                        class="w-full border-gray-300 bg-white rounded-xl py-2 px-4 mt-1 mb-2 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" />

                                    <input type="number" name="exp_years" placeholder="عدد سنوات الخبرة"
                                        value="{{ old('exp_years', $doctor->doctorProfile->exp_years) }}"
                                        class="w-full border-gray-300 bg-white rounded-xl py-2 px-4 mt-1 text-gray-700 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" />
                                    @error('exp_years')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- أزرار -->
                                <div class="flex gap-4 pt-4">
                                    <button type="button" @click="showEditModal = false"
                                        class="flex-1 py-3 bg-gray-500 text-white text-lg font-semibold rounded-xl shadow hover:bg-gray-600 transition">
                                        إلغاء
                                    </button>
                                    <button type="submit"
                                        class="flex-1 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white text-lg font-semibold rounded-xl shadow hover:from-blue-700 hover:to-indigo-800 transition">
                                        💾 حفظ التعديلات
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- حالة عدم وجود ملف مهني -->
                <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
                    <div class="max-w-md mx-auto">
                        <div
                            class="bg-indigo-100 p-4 rounded-full w-20 h-20 mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-4">لم تقم بعد بإدخال ملفك المهني</h3>
                        <p class="text-gray-600 mb-6">ابدأ بإنشاء ملفك المهني لعرض معلوماتك للزملاء والمرضى</p>
                        <a href="{{ route('doctor-profile.create') }}"
                            class="inline-flex items-center bg-gradient-to-r from-indigo-600 to-purple-700 text-white font-medium px-8 py-3 rounded-xl shadow-lg hover:from-indigo-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-1">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            إدخال الملف المهني الآن
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
@endsection
