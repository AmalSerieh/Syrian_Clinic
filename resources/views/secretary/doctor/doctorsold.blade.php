@extends('layouts.secretary.header')

@section('content')
<div x-data="{
    selectedDoctor: null,
    showSidebar: false,
    viewDoctor(doctor) {
        this.selectedDoctor = doctor;
        this.showSidebar = true;
    }
}">
    <div class="grid grid-cols-3 gap-6">
        @foreach ($doctors as $doctor)
            <div class="bg-dark-blue p-4 rounded-lg shadow text-center text-white">
                <img src="{{ Storage::url($doctor->photo) }}" alt="Doctor"
                     class="rounded-full w-24 h-24 mx-auto" />
                <h2 class="text-lg mt-2">{{ $doctor->user->name }}</h2>
                <p>
                    {{ app()->getLocale() == 'ar' ? $doctor->doctorProfile->specialist_ar ?? 'specialist' : $doctor->doctorProfile->specialist_en ?? 'specialist' }}
                </p>

                {{-- زر إظهار التفاصيل --}}
                <button
                    @click="viewDoctor(@js($doctor))"
                    class="mt-2 inline-block bg-blue-500 text-white px-4 py-1 rounded">
                    View Details
                </button>
                <a href="{{route('secretary.doctor.appointment',['id'=>$doctor->id])}}"   class="mt-2 inline-block bg-pink-500 text-white px-4 py-1 rounded">View Appointement</a>

                {{-- حالة التواجد --}}
                <div class="mt-2">
                    @if ($doctor->is_available_today)
                        <span class="bg-green-600 text-white px-3 py-1 rounded">Exist</span>
                    @else
                        <span class="bg-red-600 text-white px-3 py-1 rounded">Not Exist</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- الشريط الجانبي لعرض جدول الدوام --}}
    <div x-show="showSidebar"
         x-transition
         class="fixed right-0 top-0 h-full w-[300px] bg-white text-black p-4 shadow-xl overflow-y-auto z-50">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-bold text-lg">🩺 Doctor Schedule</h2>
            <button @click="showSidebar = false" class="text-red-500 font-bold text-xl">×</button>
        </div>

        <template x-if="selectedDoctor">
            <div>
                <img :src="selectedDoctor.photo ? '/storage/' + selectedDoctor.photo : ''"
                     class="w-24 h-24 rounded-full mx-auto border border-gray-300 mb-2" />

                <h3 class="text-center font-bold text-lg" x-text="selectedDoctor.user.name"></h3>
                <p class="text-center text-sm mb-4" x-text="selectedDoctor.doctor_profile.specialist_en"></p>

                <table class="w-full text-sm border mt-4">
                    <template x-for="day in selectedDoctor.full_schedule" :key="day.day">
                        <tr class="border-b">
                            <td class="font-semibold p-1" x-text="day.day"></td>
                            <td class="p-1 text-right" x-text="day.has_shift ? (day.start_time + ' - ' + day.end_time) : '🚫 لا يوجد دوام'"></td>
                        </tr>
                    </template>
                </table>
            </div>
        </template>
    </div>
</div>
{{--  <div x-data="{ selectedDoctor: null }" class="flex gap-6 p-6 h-full">
        <!-- Left: Doctors Grid -->
        <div class="w-3/4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($doctors as $doctor)
                <div class="bg-[#031D2E] rounded-2xl p-4 text-center text-white shadow-xl">
                    <div class="relative w-[120px] h-[120px] mx-auto mb-2 flex items-center justify-center">
                        <div class="absolute w-[120px] h-[120px] rounded-full bg-blue-700 z-[1]"></div>
                        <div
                            class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full border-[6px] border-blue-700 border-r-transparent border-b-transparent rotate-[-30deg] z-0">
                        </div>
                        <img src="{{ $doctor['image'] }}" alt="Doctor"
                            class="w-[100px] h-[100px] object-cover object-center rounded-full border-4 border-black bg-white z-[2] relative">
                    </div>


                    <h3 class="mt-4 text-lg font-semibold">{{ $doctor['name'] }}</h3>
                    <p class="text-sm text-gray-400">{{ $doctor['title'] }}</p>

                    <div class="mt-4 space-y-2">
                        <button @click="selectedDoctor = {{ json_encode($doctor) }}"
                            class="w-full bg-black py-2 rounded-xl hover:bg-gray-900 transition">
                            View Details
                        </button>

                        @if ($doctor['status'] == 'exist')
                            <button class="w-full bg-green-700 py-2 rounded-xl">Exist</button>
                        @else
                            <button class="w-full bg-red-700 py-2 rounded-xl">Not Exist</button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Right Panel -->
        <div class="w-1/4 bg-[#0e1625] p-6 rounded-3xl text-white text-center" x-show="selectedDoctor" x-cloak x-transition>
            <template x-if="selectedDoctor">
                <div class="space-y-4">
                    <!-- Doctor Image -->
                    <div class="relative w-[120px] h-[120px] mx-auto mb-2 flex items-center justify-center">
                        <div class="absolute w-[120px] h-[120px] rounded-full bg-blue-700 z-[1]"></div>
                        <div
                            class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full border-[6px] border-blue-700 border-r-transparent border-b-transparent rotate-[-30deg] z-0">
                        </div>
                        <img :src="selectedDoctor.image" alt="Doctor"
                            class="w-[100px] h-[100px] object-cover object-center rounded-full border-4 border-black bg-white z-[2] relative">
                    </div>


                    <!-- Name & Email -->
                    <h2 class="text-xl font-bold" x-text="selectedDoctor.name"></h2>
                    <p class="text-sm text-gray-400" x-text="selectedDoctor.email"></p>

                    <!-- Income -->
                    <div class="bg-green-900/30 border border-green-600 p-4 rounded-lg">
                        <h3 class="text-green-300 text-sm">Total Income</h3>
                        <p class="text-lg font-bold text-green-200">$632.000</p>
                    </div>

                    <!-- Outcome -->
                    <div class="bg-red-900/30 border border-red-600 p-4 rounded-lg">
                        <h3 class="text-red-300 text-sm">Total Outcome</h3>
                        <p class="text-lg font-bold text-red-200">$632.000</p>
                    </div>

                    <!-- Schedule -->
                    <div class="mt-4 space-y-2">
                        <template x-for="[day, time] in Object.entries(selectedDoctor.schedule)" :key="day">
                            <div class="flex justify-between items-center bg-[#02121D] px-3 py-2 rounded-lg text-sm">
                                <span class="text-gray-300" x-text="day"></span>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold"
                                    :class="{
                                        'bg-red-600 text-white': time === 'Not Available',
                                        'bg-green-600 text-white': time !== 'Not Available'
                                    }"
                                    x-text="time">
                                </span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div> --}}
@endsection
