@extends('layouts.doctor.header')

@section('content')
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Main Content --}}
    <div class="p-4 grid grid-cols-12 gap-6 -mt-5">

        {{-- Left Section --}}
        <div class="col-span-8 space-y-5">
            {{-- Today's statistics --}}
            <div class="bg-[#062E47] p-6 rounded-xl text-white">
                <h2 class="text-lg font-semibold">Today's statistics</h2>
                <h6 class="text-gray-500 text-sm mb-4">Sales summary</h6>

                <!-- Appointment Stats Cards -->
                <div class="grid grid-cols-3 gap-4 -mt-2 text-white">
                    <!-- Done -->
                    <div class="bg-green-900/30 border border-green-700 p-5 rounded-xl shadow flex items-center gap-3">
                        <div class="bg-green-700 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-green-300 text-sm">Total Done Date</h2>
                            <p class="text-xl font-bold text-green-200">14 Dates</p>
                        </div>
                    </div>

                    <!-- Canceled -->
                    <div class="bg-red-900/30 border border-red-700 p-5 rounded-xl shadow flex items-center gap-3">
                        <div class="bg-red-700 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-red-300 text-sm">Total Cancel Date</h2>
                            <p class="text-xl font-bold text-red-200">5 Dates</p>
                        </div>
                    </div>

                    <!-- All Dates -->
                    <div class="bg-yellow-900/30 border border-yellow-700 p-5 rounded-xl shadow flex items-center gap-3">
                        <div class="bg-yellow-700 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-yellow-300 text-sm">Total Dates</h2>
                            <p class="text-xl font-bold text-yellow-200">19 Dates</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Patient Info Card --}}
            <div class="bg-[#062E47] p-4 rounded-xl text-white">
                <h3 class="font-semibold mb-3">Patient Info</h3>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Box 1 --}}
                    <div class="bg-gray-900 rounded-2xl p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-white font-semibold">Public</h2>
                            <button class="p-1 rounded-md border border-blue-400 bg-blue-400/20 hover:bg-blue-400/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-black" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                            </button>
                        </div>

                        {{-- Basic Data --}}
                        <div class="grid grid-cols-4 gap-4 text-center">
                            {{-- Weight --}}
                            <div>
                                <p class="text-gray-400 text-xs flex items-center justify-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path
                                            d="M5 20h14v-2H5v2zm7-18C8.134 2 5 5.134 5 9c0 2.36 1.235 4.444 3.084 5.662l-1.51 5.69h10.852l-1.51-5.69A6.978 6.978 0 0 0 19 9c0-3.866-3.134-7-7-7z" />
                                    </svg>
                                    Weight :
                                </p>
                                <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">61.53 </p>
                            </div>

                            {{-- Height --}}
                            <div>
                                <p class="text-gray-400 text-xs flex items-center justify-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M12 2v20m6-4h-4m-4 0H6" />
                                    </svg>
                                    Height :
                                </p>
                                <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">176.3 </p>
                            </div>

                            {{-- Gender --}}
                            <div>
                                <p class="text-gray-400 text-xs flex items-center justify-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M5.5 21h13" />
                                    </svg>
                                    Gender :
                                </p>
                                <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">Male</p>
                            </div>

                            {{-- Blood Group --}}
                            <div>
                                <p class="text-gray-400 text-xs flex items-center justify-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M12 21a9 9 0 0 0 9-9c0-4.97-9-13-9-13S3 7.03 3 12a9 9 0 0 0 9 9z" />
                                    </svg>
                                    Blood :
                                </p>
                                <p class="bg-[#11283f] text-blue-300 rounded-md px-3 py-1 mt-1">O+</p>
                            </div>
                        </div>

                        {{-- Addictions --}}
                        <div class="mt-4">
                            <h2 class="text-gray-400 font-semibold mb-2">Addictions :</h2>
                            <div class="flex flex-wrap gap-2 justify-center">
                                <span class="bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">Smoking</span>
                                <span class="bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">Alcohol</span>
                                <span class="bg-[#11283f] text-blue-300 px-3 py-1 rounded-md">Drugs</span>
                            </div>
                        </div>
                    </div>

                    {{-- Box 2 --}}
                    <div class="bg-gray-900 p-4 rounded-xl">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold mb-2">Sensitivity</h3>
                            <button class="p-1 rounded-md border border-blue-400 bg-blue-400/20 hover:bg-blue-400/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-black" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 5v14M5 12h14" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-gray-400 text-sm">No sensitivity data</p>
                    </div>

                    {{-- Box 3 --}}
                    <div class="bg-gray-900 p-4 rounded-lg min-h-[200px] flex flex-col justify-center">
                        <p class="text-gray-400">Extra Info 1</p>
                    </div>

                    {{-- Box 4 --}}
                    <div class="bg-gray-900 p-4 rounded-lg min-h-[200px] flex flex-col justify-center">
                        <p class="text-gray-400">Extra Info 2</p>
                    </div>
                </div>
            </div>

            {{-- Bottom Buttons + Case --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col gap-4 h-full">
                    <button
                        class="flex-1 border-2 border-dashed border-red-500 text-red-500 rounded-xl hover:bg-red-500/20 hover:text-white transition text-lg font-semibold">
                        Cancel
                    </button>
                    <button
                        class="flex-1 border-2 border-dashed border-yellow-500 text-yellow-500 rounded-xl hover:bg-yellow-500/20 hover:text-white transition text-lg font-semibold">
                        Postponement
                    </button>
                    <button
                        class="flex-1 border-2 border-dashed border-blue-500 text-blue-500 rounded-xl hover:bg-blue-500/20 hover:text-white transition text-lg font-semibold">
                        New nurse
                    </button>
                </div>
                <div>
                    <p class="text-lg">case description</p>
                    <textarea class="bg-transparent border border-blue-500 text-white rounded-xl p-2 w-full min-h-[160px]"></textarea>
                </div>
            </div>
        </div>

        {{-- Right Section --}}
        <div class="col-span-4 space-y-6">
            <div class="p-4 space-y-6 text-white bg-[#062E47] rounded-lg">
                {{-- Patients in the clinic --}}
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <h2 class="text-lg font-semibold">Patients in the clinic</h2>
                        <button class="text-blue-400 hover:underline flex items-center gap-1">DR. Omar <svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-chevron-down-icon lucide-chevron-down">
                                <path d="m6 9 6 6 6-6" />
                            </svg></button>
                    </div>
                    <ul class="space-y-3">
                        <li class="flex items-center justify-between bg-[#0e1b26] p-3 rounded-md">
                            <div class="flex items-center space-x-3">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Patient"
                                    class="w-10 h-10 rounded-full object-cover" />
                                <div>
                                    <p class="font-semibold">Adobe After Effect</p>
                                    <p class="text-sm text-gray-400">Review</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs rounded-full bg-red-600/35 text-red-600">Finished</span>
                        </li>
                        <li class="flex items-center justify-between bg-[#0e1b26] p-3 rounded-md">
                            <div class="flex items-center space-x-3">
                                <img src="https://randomuser.me/api/portraits/men/53.jpg" alt="Patient"
                                    class="w-10 h-10 rounded-full object-cover" />
                                <div>
                                    <p class="font-semibold">Mcdonald's</p>
                                    <p class="text-sm text-gray-400">Appointment</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs rounded-full bg-green-600/35 text-green-600">Next</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Medical prescription --}}
            <div class="bg-[#062E47] p-4 rounded-md">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-semibold text-lg">Medical prescription</h2>
                    <button
                        class="bg-blue-500/20 border-2 border-blue-500 border-opacity-50 p-1 rounded inline-flex items-center justify-center">
                        <!-- plus icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-black" width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="inline-block text-white">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </button>

                </div>

                <!-- Single medication card -->
                <div class="p-3 rounded-md  max-w-xl">
                    <!-- العنوان والحالة -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-semibold">Medication 1</span>
                        <span
                            class="inline-block px-2 py-0.5 text-xs rounded-full bg-green-600/40 text-green-600">waiting</span>
                    </div>

                    <!-- الصف الأول: 4 أعمدة -->
                    <div class="grid grid-cols-4 gap-4 text-xs text-gray-400">
                        <div>Type</div>
                        <div>Medical name</div>
                        <div>Trade name</div>
                        <div>Quantity</div>
                    </div>
                    <div class="grid grid-cols-4 gap-4 mt-1 text-sm ">
                        <div>Pills</div>
                        <div>Aspiren 81</div>
                        <div>Aspiren 81</div>
                        <div>30 pills</div>
                    </div>

                    <!-- الصف الثاني: 3 أعمدة -->
                    <div class="grid grid-cols-3 gap-4 text-xs text-gray-400 mt-4">
                        <div>Dosage</div>
                        <div>Time</div>
                        <div>Status</div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-1 text-sm ">
                        <div>2 pills</div>
                        <div>After eat</div>
                        <div>waiting</div>
                    </div>

                    <!-- الصف الثالث: alternative -->
                    <div class="flex items-center mt-4 text-xs text-gray-400">
                        <div class="w-1/4">Alternative :</div>
                        <div class="w-3/4 text-sm text-white">[ Aspiren 81, asc, vds ]</div>
                    </div>
                </div>
            </div>

            {{-- Patients in the clinic (second list) --}}
            <div class="bg-[#062E47] p-4 rounded-md">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="font-semibold text-lg">Patients in the clinic</h2>
                    <button
                        class="bg-blue-500/20 border-2 border-blue-500 border-opacity-50 p-1 rounded inline-flex items-center justify-center">
                        <!-- plus icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-black" width="18" height="18"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" class="inline-block text-white">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </button>

                </div>

                <ul class="space-y-1">
                    <li class="flex items-center justify-between bg-[#162a3a] p-3 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <img src="https://randomuser.me/api/portraits/men/20.jpg" alt="Patient"
                                class="w-10 h-10 rounded-full object-cover" />
                            <div>
                                <p class="font-semibold text-white">Levi's</p>
                                <p class="text-sm text-gray-400">Review</p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 text-xs rounded-full bg-green-600/35 text-green-600 font-semibold">waiting</span>
                    </li>

                    <li class="flex items-center justify-between bg-[#162a3a] p-3 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <img src="https://randomuser.me/api/portraits/women/30.jpg" alt="Patient"
                                class="w-10 h-10 rounded-full object-cover" />
                            <div>
                                <p class="font-semibold text-white">Sara K.</p>
                                <p class="text-sm text-gray-400">Consultation</p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 text-xs rounded-full bg-green-600/35 text-green-600 font-semibold">waiting</span>
                    </li>

                    <li class="flex items-center justify-between bg-[#162a3a] p-3 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <img src="https://randomuser.me/api/portraits/men/40.jpg" alt="Patient"
                                class="w-10 h-10 rounded-full object-cover" />
                            <div>
                                <p class="font-semibold text-white">John D.</p>
                                <p class="text-sm text-gray-400">Follow up</p>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 text-xs rounded-full bg-green-600/35 text-green-600 font-semibold">waiting</span>
                    </li>
                </ul>
            </div>

            {{-- Scout price --}}
            <div class="bg-[#062E47] p-4 rounded-md">
                <h3 class="font-semibold mb-3">Scout price</h3>

                <div class="flex justify-between items-center mb-2 text-blue-300">
                    <span class="text-sm">Total consumption</span>
                    <span class="bg-[#072a3a] px-3 py-1 rounded text-sm font-semibold">3 $</span>
                </div>

                <div class="flex justify-between items-center mb-3 text-blue-300">
                    <span class="text-sm">price</span>
                    <span class="bg-[#072a3a] px-3 py-1 rounded text-sm font-semibold">0 $</span>
                </div>

                <textarea class="w-full p-2 text-white bg-[#062E47] rounded-md resize-none border-2 border-blue-500" rows="1"></textarea>

            </div>



        </div>
    @endsection
