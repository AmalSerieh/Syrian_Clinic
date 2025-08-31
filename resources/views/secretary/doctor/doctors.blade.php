@extends('layouts.secretary.header')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div x-data="{
        selectedDoctor: {
            name: '{{ $doctors[0]->user->name ?? '' }}',
            email: '{{ $doctors[0]->user->email ?? '' }}',
            image: '{{ asset('storage/' . ($doctors[0]->photo ?? 'avatars/defaults.jpg')) }}',
            role: '{{ app()->getLocale() == 'ar' ? $doctors[0]->doctorProfile->specialist_ar ?? 'specialist' : $doctors[0]->doctorProfile->specialist_en ?? 'specialist' }}',
            schedule: @js($doctors[0]->full_schedule ?? [])
        },
        viewDoctor(name, email, image, role, schedule) {
            this.selectedDoctor = { name, email, image, role, schedule };
        }
    }" class="flex min-h-screen  p-4 gap-10 rounded-3xl -mt-4">
        <!-- Left: Doctors Grid -->
        <div class="w-3/4 flex flex-col">
            <div class="grid grid-cols-3 gap-6 h-full">
                @foreach ($doctors as $doctor)
                    <div
                        class="w-[238px] h-[377px] rounded-[28px] bg-[#0094e71a] backdrop-blur-[160px] border border-[#0094e7]/50 shadow-[0_64px_64px_-32px_rgba(41,0,0,0.56)] overflow-hidden transition-transform duration-300 hover:scale-[1.02] flex flex-col justify-between ">
                        <div class="flex flex-col items-center justify-center flex-grow w-full">

                            <!-- Doctor Image -->
                            <div class="relative w-[120px] h-[120px] mx-auto mb-2 flex items-center justify-center">
                                @php
                                    $colors = [
                                        ['bg' => 'bg-blue-500', 'border' => 'border-blue-500'],
                                        ['bg' => 'bg-red-500', 'border' => 'border-red-500'],
                                        ['bg' => 'bg-yellow-500', 'border' => 'border-yellow-500'],
                                        ['bg' => 'bg-green-500', 'border' => 'border-green-500'],
                                        ['bg' => 'bg-purple-500', 'border' => 'border-purple-500'],
                                        ['bg' => 'bg-pink-500', 'border' => 'border-pink-500'],
                                        ['bg' => 'bg-indigo-500', 'border' => 'border-indigo-500'], // إضافة
                                        ['bg' => 'bg-teal-500', 'border' => 'border-teal-500'], // إضافة

                                        ['bg' => 'bg-blue-400', 'border' => 'border-blue-500'],
                                        ['bg' => 'bg-red-300', 'border' => 'border-red-500'],
                                        ['bg' => 'bg-yellow-200', 'border' => 'border-yellow-500'],
                                        ['bg' => 'bg-green-600', 'border' => 'border-green-700'],
                                        ['bg' => 'bg-purple-300', 'border' => 'border-purple-400'],
                                        ['bg' => 'bg-gray-300', 'border' => 'border-gray-400'],
                                        ['bg' => 'bg-amber-300', 'border' => 'border-amber-400'],
                                        ['bg' => 'bg-grey-300', 'border' => 'border-grey-400'],
                                    ];
                                    $colorSet = $colors[array_rand($colors)];

                                @endphp
                                <div class="absolute w-[120px] h-[120px] rounded-full {{ $colorSet['bg'] }} z-[1]"></div>
                                <div
                                    class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full
                                border-[6px] {{ $colorSet['border'] }}  border-b-transparent
                                rotate-[-125deg] z-0">
                                </div>
                                <img src="{{ Storage::url($doctor->photo) }}" alt="Doctor"
                                    class="w-[100px] h-[100px] object-cover object-center rounded-full
                                border-4 border-black bg-white z-[2] relative">
                            </div>

                            <!-- Doctor Name & Title -->
                            <h3 class="mt-4 text-lg font-semibold">{{ $doctor->user->name }}</h3>
                            <p class="text-sm text-gray-400">
                                {{ app()->getLocale() == 'ar'
                                    ? $doctor->doctorProfile->specialist_ar ?? 'specialist'
                                    : $doctor->doctorProfile->specialist_en ?? 'specialist' }}
                            </p>
                        </div>
                        <div class="flex flex-col gap-2 p-4">
                            <button
                                @click="selectedDoctor = {
                                name: '{{ $doctor->user->name }}',
                                email: '{{ $doctor->user->email }}',
                                image: '{{ Storage::url($doctor->photo) }}',
                                role: '{{ app()->getLocale() == 'ar' ? $doctor->doctorProfile->specialist_ar ?? 'specialist' : $doctor->doctorProfile->specialist_en ?? 'specialist' }}',
                                status: '{{ $doctor->is_available_today ? 'exist' : 'not exist' }}',
                                schedule: @js($doctor->full_schedule),
                                bgColor: '{{ $colorSet['bg'] }}',
                                borderColor: '{{ $colorSet['border'] }}'
                                }"
                                class="w-full bg-black py-2 rounded-xl hover:bg-gray-900 transition">
                                View Details
                            </button>

                            @if ($doctor->is_available_today)
                                <button class="w-full bg-green-900 py-2 rounded-xl">Exist</button>
                            @else
                                <button class="w-full bg-red-900 py-2 rounded-xl">Not Exist</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

         <!-- Right Side (Info Panel) -->
            <div class="w-1/4 h-screen flex flex-col">
                <div
                    class="flex-grow bg-[#0094E7]/20 p-6 rounded-3xl flex flex-col items-center justify-start text-center border border-blue-500 relative h-full">

                    <template x-if="selectedDoctor">
                        <div class="flex flex-col items-center w-full h-full">

                            <!-- المعلومات الأساسية (ثابتة فوق) -->
                            <div class="shrink-0 flex flex-col items-center space-y-1">
                                <div class="relative w-[160px] h-[160px] mx-auto mb-4 flex items-center justify-center">
                                    <!-- خلفية ملونة -->
                                    <div class="absolute w-[160px] h-[160px] rounded-full bg-blue-500 z-[1]"></div>
                                    <!-- الحزام الخارجي -->
                                    <div
                                        class="absolute w-[180px] h-[180px] -top-[10px] -left-[10px] rounded-full border-[6px] border-blue-500 border-b-transparent rotate-[-125deg] z-0">
                                    </div>
                                    <!-- صورة الدكتور -->
                                    <img :src="selectedDoctor.image" alt="Doctor"
                                        class="w-[140px] h-[140px] object-cover object-center rounded-full border bg-white z-[2] relative">
                                </div>
                                <h3 class="text-white font-semibold text-xl" x-text="selectedDoctor.name"></h3>
                                <p class="text-gray-400 text-sm" x-text="selectedDoctor.role"></p>
                                <p class="text-gray-400 text-sm" x-text="selectedDoctor.email"></p>
                            </div>


                            <div class="flex-1 overflow-y-auto w-full mt-6 space-y-4"
                                style="-ms-overflow-style:none;scrollbar-width:none;">

                                <!-- Schedule -->
                                <div class="space-y-2">
                                    <template x-for="day in selectedDoctor.schedule" :key="day.day">
                                        <div
                                            class="flex justify-between items-center bg-[#02121D] px-3 py-2 rounded-lg text-sm">
                                            <span class="text-gray-300" x-text="day.day"></span>
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                                :class="{
                                                    'bg-red-600 text-white': !day.has_shift,
                                                    'bg-green-600 text-white': day.has_shift
                                                }"
                                                x-text="day.has_shift ? (day.start_time + ' - ' + day.end_time) : '🚫 لا يوجد دوام'">
                                            </span>
                                        </div>
                                    </template>
                                </div>

                                <!-- الكروت المالية -->
                                <div class="flex flex-col gap-4 pt-4">
                                    <!-- Red Card -->
                                    <div
                                        class="bg-red-900/30 border border-red-700 p-4 rounded-lg shadow flex items-center justify-between gap-3 w-full">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-red-700 p-2 rounded-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h2 class="font-semibold text-red-300 text-sm">Total Outcome</h2>
                                                <p class="text-xl font-bold text-red-200">$632.000</p>
                                            </div>
                                        </div>
                                        <p class="text-xs text-red-400 whitespace-nowrap">+1.26%</p>
                                    </div>

                                    <!-- Green Card -->
                                    <div
                                        class="bg-green-900/30 border border-green-700 p-4 rounded-lg shadow flex items-center justify-between gap-3 w-full">
                                        <div class="flex items-center gap-3">
                                            <div class="bg-green-700 p-2 rounded-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                                </svg>
                                            </div>
                                            <div>
                                                <h2 class="font-semibold text-green-300 text-sm">Total Income</h2>
                                                <p class="text-xl font-bold text-green-200">$632.000</p>
                                            </div>
                                        </div>
                                        <p class="text-xs text-green-400 whitespace-nowrap">+1.29%</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </template>

                    <template x-if="!selectedDoctor">
                        <p class="text-gray-500 mt-20">Select a doctor to view details</p>
                    </template>
                </div>
            </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function doctorDetails() {
            return {
                selectedDoctor: null,
                viewDoctor(name, email, image, role, schedule) {
                    this.selectedDoctor = {
                        name,
                        email,
                        image,
                        role,
                        schedule // الآن هذا Array صالح


                    };
                }
            }

        }
    </script>
@endsection
