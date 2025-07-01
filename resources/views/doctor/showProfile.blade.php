<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('الملف المهني ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- عرض الملف المهني للطبيب -->
            @php
                $doctor = Auth::user()->doctor;
            @endphp

            @if ($doctor)
                <div class="mt-6">
                    <a href="{{ route('doctor-profile.edit', $doctor->doctorProfile->id) }}"
                        class="inline-block bg-red-600 text-white text-sm font-medium px-6 py-2 rounded-lg shadow hover:bg-blue-700 transition duration-200">
                        تعديل الملف المهني
                    </a>
                </div>



                <div class="bg-white shadow sm:rounded-lg p-6">

                    <h3 class="text-lg font-semibold mb-4 text-gray-800">الملف المهني</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <strong>Name:</strong> {{ Auth::user()->name }}
                        </div>
                        <div>
                            <strong>specialist:</strong> {{ $doctor->doctorProfile->specialist }}
                        </div>
                        <div>
                            <strong>gender:</strong> {{ $doctor->doctorProfile->gender == 'male' ? 'ذكر' : 'أنثى' }}
                        </div>
                        <div>
                            <strong> date_birth:</strong> {{ $doctor->doctorProfile->date_birth }}
                        </div>
                        <div class="md:col-span-2">
                            <strong> biography:</strong>
                            <p class="mt-1 text-gray-700">{{ $doctor->doctorProfile->biography }}</p>
                        </div>

                        <!-- معلومات الشهادة -->
                        <div class="md:col-span-2 mt-4 border-t pt-4">
                            <h4 class="text-md font-semibold text-gray-700 mb-2">certification:</h4>
                            <p><strong>cer_name:</strong> {{ $doctor->doctorProfile->cer_name }}</p>
                            <p><strong>cer_place:</strong> {{ $doctor->doctorProfile->cer_place }}</p>
                            <p><strong> cer_date:</strong> {{ $doctor->doctorProfile->cer_date }}</p>
                            <p><strong> cer_images:</strong>
                                @if ($doctor->doctorProfile->cer_images)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $doctor->doctorProfile->cer_images) }}"
                                            class="w-28 h-20 object-cover rounded-full" alt="صورة الشهادة">
                                    </div>
                                @endif
                            </p>
                        </div>

                        <!-- معلومات الخبرة -->
                        <div class="md:col-span-2 mt-4 border-t pt-4">
                            <h4 class="text-md font-semibold text-gray-700 mb-2">experiance</h4>
                            <p><strong>exp_place :</strong> {{ $doctor->doctorProfile->exp_place }}</p>
                            <p><strong>exp_years :</strong> {{ $doctor->doctorProfile->exp_years }}</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white shadow sm:rounded-lg p-6">
                    <p class="text-gray-700">لم تقم بعد بإدخال ملفك المهني.</p>
                    <a href="{{ route('doctor-profile.create') }}"
                        class="mt-3 inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        إدخال الملف المهني الآن
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
