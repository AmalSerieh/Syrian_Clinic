@extends('layouts.secretary.header')

@section('content')
    <div x-data="{ selectedDoctor: null }" class="relative p-6 h-full">
        <!-- Left: Doctors Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($doctors as $doctor)
                <div class="bg-[#031D2E] rounded-2xl p-4 text-center text-white shadow-xl">
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

                    <div class="mt-4 space-y-2">
                        <button
                            @click="selectedDoctor = {
                name: '{{ $doctor->user->name }}',
                email: '{{ $doctor->user->email }}',
                image: '{{ Storage::url($doctor->photo) }}',
                specialist: '{{ app()->getLocale() == 'ar' ? $doctor->doctorProfile->specialist_ar ?? 'specialist' : $doctor->doctorProfile->specialist_en ?? 'specialist' }}',
                status: '{{ $doctor->is_available_today ? 'exist' : 'not exist' }}',
                schedule: @js($doctor->full_schedule),
                bgColor: '{{ $colorSet['bg'] }}',
                borderColor: '{{ $colorSet['border'] }}'
            }"
                            class="w-full bg-black py-2 rounded-xl hover:bg-gray-900 transition">
                            View Details
                        </button>

                        @if ($doctor->is_available_today)
                            <button class="w-full bg-green-700 py-2 rounded-xl">Exist</button>
                        @else
                            <button class="w-full bg-red-700 py-2 rounded-xl">Not Exist</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Sidebar Overlay -->
        <div x-show="selectedDoctor" x-transition.opacity @click="selectedDoctor = null"
            class="fixed inset-0 bg-black bg-opacity-50 z-40" x-cloak>
        </div>

        <!-- Sidebar Panel -->
        <div x-show="selectedDoctor" x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed right-0 top-0 h-full w-96 bg-[#0e1625] p-6 rounded-l-3xl text-white z-50 shadow-lg overflow-y-auto"
            x-cloak>

            <!-- Close Button -->
            <button @click="selectedDoctor = null"
                class="absolute top-4 right-4 text-gray-400 hover:text-white text-2xl">&times;</button>

            <div class="space-y-4 mt-6">
                <!-- Doctor Image -->
                <div class="relative w-[120px] h-[120px] mx-auto mb-2 flex items-center justify-center">
                    <div :class="`absolute w-[120px] h-[120px] rounded-full ${selectedDoctor.bgColor} z-[1]`"></div>
                    <div
                        :class="`absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full border-[6px] ${selectedDoctor.borderColor}  border-b-transparent rotate-[-125deg] z-0`">
                    </div>
                    <img :src="selectedDoctor.image" alt="Doctor"
                        class="w-[100px] h-[100px] object-cover object-center rounded-full border-4 border-black bg-white z-[2] relative">
                </div>

                <!-- Name & Email -->
                <div class="text-center">
                    <h2 class="text-xl font-bold justify-center" x-text="selectedDoctor.name"></h2>
                    <p class="text-sm text-gray-400" x-text="selectedDoctor.specialist"></p>
                    <p class="text-sm text-gray-400" x-text="selectedDoctor.email"></p>
                </div>

                <!-- Income -->
                <div class="bg-green-900/30 border border-green-600 p-4 rounded-lg text-center">
                    <h3 class="text-green-300 text-sm">Total Income</h3>
                    <p class="text-lg font-bold text-green-200">$632.000</p>
                </div>

                <!-- Outcome -->
                <div class="bg-red-900/30 border border-red-600 p-4 rounded-lg text-center">
                    <h3 class="text-red-300 text-sm">Total Outcome</h3>
                    <p class="text-lg font-bold text-red-200">$632.000</p>
                </div>

                <!-- Schedule -->
                <div class="mt-4 space-y-2">
                    <template x-for="day in selectedDoctor.schedule" :key="day.day">
                        <div class="flex justify-between items-center bg-[#02121D] px-3 py-2 rounded-lg text-sm">
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
            </div>
        </div>
    </div>
@endsection
