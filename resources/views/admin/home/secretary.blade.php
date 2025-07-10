@extends('layouts.admin.header')

@section('content')
    {{-- رسالة نجاح --}}
    @if (session('message'))
        <div class="mb-4 px-4 py-3 bg-green-100 text-orange-800 rounded shadow">
            {{ session('message') }}
        </div>
    @endif

    @if ($secretary)
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 mt-4">
            <!-- بطاقة السكرتيرة -->
            <div class="bg-[#19222f] rounded-2xl p-6 text-center h-[400px]">
                <img src=" {{ asset('storage/' . $secretary->secretary->photo) }}" alt="Secretary"
                    class="w-48 h-48 mx-auto rounded-full mb-4 object-cover border-4 border-blue-900 mt-6">
                <h3 class="text-4xl font-semibold mt-6">{{ $secretary->name }}</h3>
                <p class="text-gray-400">{{ $secretary->email }}</p>
            </div>

            <!-- تفاصيل الموعد -->
            <div class="bg-[#19222f] rounded-2xl p-6 text-left space-y-6">
                <div>
                    <label class="text-gray-400 text-sm block mb-1">Phone Number</label>
                    <p class="w-full border-b border-gray-600 text-white text-lg pb-1">
                        {{ $secretary->phone }}
                    </p>
                </div>

                <div>
                    <label class="text-gray-400 text-sm block mb-1">Date of Appointment</label>
                    <p class="w-full border-b border-gray-600 text-white text-lg pb-1">
                        {{ $secretary->secretary->date_of_appointment }}
                    </p>
                </div>

                <div>
                    <label class="text-gray-400 text-sm block mb-1">Gender</label>
                    <p class="w-full border-b border-gray-600 text-white text-lg pb-1 capitalize">
                        {{ $secretary->secretary->gender }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start w-full mt-6">
            <!-- الزر الأول -->
            @if(!$secretary)


            <a href="{{ route('admin.secretary.add') }}">
                <div
                    class="ml-8 border-2 border-dashed border-blue-500 text-white rounded-3xl w-[550px] h-[100px] flex flex-row items-center justify-center gap-4 shadow-md bg-[#12192b] hover:scale-[1.02] transition-transform duration-300 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-user-round-plus text-blue-300">
                        <path d="M2 21a8 8 0 0 1 13.292-6" />
                        <circle cx="10" cy="8" r="5" />
                        <path d="M19 16v6" />
                        <path d="M22 19h-6" />
                    </svg>
                    <div class="text-lg font-semibold">Add Secretary</div>
                </div>
            </a>
             @endif

            <!-- الزر الثاني -->
            <a href="{{ route('admin.secretary.replace', [$secretary->id]) }}">
            <div
                class="ml-8 border-2 border-dashed border-red-500 text-white rounded-3xl w-[550px] h-[100px] flex flex-row items-center justify-center gap-4 shadow-md bg-[#12192b] hover:scale-[1.02] transition-transform duration-300 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-repeat text-red-400">
                    <path d="m17 2 4 4-4 4" />
                    <path d="M3 11v-1a4 4 0 0 1 4-4h14" />
                    <path d="m7 22-4-4 4-4" />
                    <path d="M21 13v1a4 4 0 0 1-4 4H3" />
                </svg>
                <div class="text-lg font-semibold">Replacing Secretary</div>
            </div>
            </a>
        </div>
    @else
        <div class="flex justify-center items-center min-h-[200px]">
    <div class="bg-yellow-50 border border-yellow-300 rounded-3xl p-6 shadow-lg text-center max-w-xl w-full">
        <div class="text-yellow-700 font-semibold text-lg mb-4 flex items-center justify-center gap-2">
            ⚠️ لم يتم إضافة سكرتيرة بعد.
        </div>
        <a href="{{ route('admin.secretary.add') }}">
            <div
                class="mt-4 flex items-center justify-center gap-4 border-2 border-dashed border-blue-500 bg-gradient-to-r from-[#1a2537] to-[#12192b] rounded-2xl px-6 py-4 text-white shadow-md hover:scale-105 hover:shadow-lg transition-transform duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-user-round-plus text-blue-400">
                    <path d="M2 21a8 8 0 0 1 13.292-6" />
                    <circle cx="10" cy="8" r="5" />
                    <path d="M19 16v6" />
                    <path d="M22 19h-6" />
                </svg>
                <span class="text-lg font-semibold text-blue-300">Add Secretary</span>
            </div>
        </a>
    </div>
</div>


    @endif
@endsection
