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
                                    <div class="absolute w-[120px] h-[120px] rounded-full bg-blue-700 z-[1]"></div>
                                    <div
                                        class="absolute w-[140px] h-[140px] -top-[10px] -left-[10px] rounded-full border-[6px] border-blue-700 border-r-transparent border-b-transparent rotate-[-30deg] z-0">
                                    </div>
                                    <img src="{{ asset('storage/' . $doctor->photo) }}"
                                        alt="Doctor"
                                        class="w-[100px] h-[100px] object-cover object-center rounded-full border-4 border-black bg-white z-[2] relative">
                                </div>
                                <div class="text-center">
                                    <h3 class="text-white font-semibold">{{ $doctor->user->name }}</h3>
                                    <p class="text-gray-400 text-sm">{{ $doctor->doctorProfile->specialist_ar ?? 'Specialty' }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2 w-full justify-end mt-4">
                                <button
                                    @click="viewDoctor(
                                        '{{ $doctor->user->name }}',
                                        '{{ $doctor->user->email }}',
                                        '{{ asset('storage/' . $doctor->photo) }}',
                                        '{{ $doctor->doctorProfile->specialist_ar ?? 'Specialty' }}'
                                    )"
                                    class="bg-black bg-opacity-60 text-white py-3 rounded-full text-sm">
                                    View Details
                                </button>
                                <button class="bg-red-900 bg-opacity-60 text-white py-3 rounded-full text-sm">Delete</button>
                            </div>
                        </div>
                    @endforeach

                    <!-- إضافة طبيب -->
                    <a href="{{ route('admin.doctor.add') }}" class="block">
                        <div
                            class="p-4 rounded-3xl flex flex-col items-center justify-center cursor-pointer hover:bg-blue-500/10 transition h-[350px] border-2 border-dashed border-blue-500 bg-transparent">
                            <div class="text-blue-500 text-6xl mb-2 font-bold select-none">+</div>
                            <div class="w-full block text-center text-xl text-blue-500 py-2 px-4 rounded-md font-semibold">
                                Add Doctor
                            </div>
                        </div>
                    </a>
                @else
                    <div class="col-span-3 flex items-center justify-center">
                        <a href="{{ route('admin.doctor.add') }}">
                            <div
                                class="p-10 rounded-3xl flex flex-col items-center justify-center cursor-pointer hover:bg-blue-500/10 transition border-2 border-dashed border-blue-500 bg-transparent">
                                <div class="text-blue-500 text-8xl mb-4 font-bold select-none">+</div>
                                <div class="text-2xl text-blue-500 py-2 px-4 rounded-md font-semibold">
                                    Add First Doctor
                                </div>
                            </div>
                        </a>
                    </div>
                @endif

            </div>
        </div>

        <!-- Right Side (Info Panel) -->
        <div class="w-1/4 flex flex-col">
            <div class="flex-grow bg-[#0e1625] p-6 rounded-3xl flex flex-col items-center justify-start text-center relative">

                <template x-if="selectedDoctor">
                    <div class="mt-8 flex flex-col items-center w-full space-y-4">
                        <div class="w-40 h-40 rounded-full overflow-hidden border-4 border-black">
                            <img :src="selectedDoctor.image" alt="Doctor" class="w-full h-full object-cover rounded-full" />
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

    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        function doctorDetails() {
            return {
                selectedDoctor: null,
                viewDoctor(name, email, image, role) {
                    this.selectedDoctor = { name, email, image, role };
                }
            }
        }
    </script>
@endsection
