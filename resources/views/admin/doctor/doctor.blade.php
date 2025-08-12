@extends('layouts.admin.header')
@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex min-h-screen bg-[#1e293b] p-4 gap-10 rounded-3xl -mt-4" x-data="doctorDetails()">

        <!-- Left Side (Doctors List) -->
        <div class="w-3/4 flex flex-col">
            <div class="grid grid-cols-3 gap-6 h-full">

                @if ($doctors->count())
                    @foreach ($doctors as $doctor)
                        <div class="bg-[#0e1625] p-4 rounded-3xl flex flex-col items-center h-[350px]">
                            <div class="flex flex-col items-center justify-center flex-grow w-full">
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
                                    <div class="absolute w-[120px] h-[120px] rounded-full  {{ $colorSet['bg'] }} z-[1]">
                                    </div>
                                    <div
                                        class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full border-[6px] {{ $colorSet['border'] }} border-b-transparent rotate-[-125deg] z-0">
                                    </div>

                                    <img src="{{ asset('storage/' . $doctor->photo) }}" alt="Doctor"
                                        class="w-[100px] h-[100px] object-cover object-center rounded-full border-1 border-black bg-white z-[2] relative">
                                </div>
                                <div class="text-center">
                                    <h3 class="text-white font-semibold">{{ $doctor->user->name }}</h3>
                                    {{ app()->getLocale() == 'ar' ? $doctor->doctorProfile->specialist_ar ?? 'specialist' : $doctor->doctorProfile->specialist_en ?? 'specialist' }}
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 w-full justify-end mt-4">
                                <button
                                    @click="viewDoctor(
                                        '{{ $doctor->user->name }}',
                                        '{{ $doctor->user->email }}',
                                        '{{ asset('storage/' . $doctor->photo) }}',
                                        '{{ app()->getLocale() == 'ar' ? $doctor->doctorProfile->specialist_ar ?? 'specialist' : $doctor->doctorProfile->specialist_en ?? 'specialist' }}'
                                    )"
                                    class="bg-black bg-opacity-60 text-white py-3 rounded-full text-sm">
                                    View Details
                                </button>
                                <button
                                    class="bg-red-900 bg-opacity-60 text-white py-3 rounded-full text-sm">Delete</button>
                            </div>
                        </div>
                    @endforeach

                    @if ($roomsAreFull)
                        <div
                            class="p-4 rounded-3xl flex flex-col items-center justify-center cursor-pointer h-[350px] border-2 border-dashed border-red-500 bg-transparent">
                            <div class="text-red-500 text-6xl mb-2 font-bold select-none">!</div>
                            <div class="w-full block text-center text-xl text-red-500 py-2 px-4 rounded-md font-semibold">
                                غرف العيادة ممتلئة
                            </div>
                            <div class="text-white text-sm mt-2">
                                يرجى حذف طبيب لإضافة طبيب جديد
                            </div>
                        </div>
                    @else
                        <!-- إضافة طبيب -->
                        <a href="{{ route('admin.doctor.add') }}" class="block">
                            <div
                                class="p-4 rounded-3xl flex flex-col items-center justify-center cursor-pointer hover:bg-blue-500/10 transition h-[350px] border-2 border-dashed border-blue-500 bg-transparent">
                                <div class="text-blue-500 text-6xl mb-2 font-bold select-none">+</div>
                                <div
                                    class="w-full block text-center text-xl text-blue-500 py-2 px-4 rounded-md font-semibold">
                                    Add Doctor
                                </div>
                            </div>
                        </a>
                    @endif
                @else
                    <div class="col-span-3 flex items-center ">
                        @if ($roomsAreFull)
                            <div class="mt-4 bg-red-600 text-white font-bold py-2 px-4 rounded-2xl w-full text-center">
                                غرف العيادة ممتلئة! يرجى حذف طبيب لإضافة طبيب جديد.
                            </div>
                        @else
                            <div class="col-span-3 flex items-center  -mt-96 ">
                                <a href="{{ route('admin.doctor.add') }}">
                                    <div
                                        class="p-10 rounded-3xl flex flex-col items-center justify-center cursor-pointer hover:bg-blue-500/10 transition border-2 border-dashed border-blue-500 bg-transparent">
                                        <div class="text-blue-500 text-8xl mb-4 font-bold select-none">+</div>
                                        <div class="text-2xl text-blue-500 py-8 px-4 rounded-md font-semibold">
                                            Add First Doctor
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
        @if ($doctors->count())
            <!-- Right Side (Info Panel) -->
            <div class="w-1/4 flex flex-col">
                <div
                    class="flex-grow bg-[#0e1625] p-6 rounded-3xl flex flex-col items-center justify-start text-center relative">

                    <template x-if="selectedDoctor">
                        <div class="mt-8 flex flex-col items-center w-full space-y-4">
                            <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-black">
                                <img :src="selectedDoctor.image" alt="Doctor"
                                    class="w-full h-full object-cover rounded-full" />
                            </div>
                            <h3 class="text-white font-semibold text-xl" x-text="selectedDoctor.name"></h3>
                            <p class="text-gray-400 text-sm" x-text="selectedDoctor.role"></p>
                            <p class="text-gray-400 text-sm" x-text="selectedDoctor.email"></p>
                            <div class="w-full flex flex-col gap-4 pt-20">
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

                    </template>

                    <template x-if="!selectedDoctor">
                        <p class="text-gray-500 mt-20">Select a doctor to view details</p>

                    </template>
                </div>
            </div>
        @endif

    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function doctorDetails() {
            return {
                selectedDoctor: null,
                viewDoctor(name, email, image, role) {
                    this.selectedDoctor = {
                        name,
                        email,
                        image,
                        role
                    };
                }
            }
        }
    </script>
@endsection
