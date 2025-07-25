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
@endsection
