@extends('layouts.doctor.header')

@section('content')
<div class="container mx-auto mt-6">
    <h1 class="text-2xl font-bold mb-6 text-center text-blue-700">قائمة الممرضين</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($nurses as $nurse)
            <div class="bg-white shadow-lg rounded-2xl p-4">
                {{-- صورة الممرضة --}}
                <div class="flex justify-center mb-4">
                    @if($nurse->photo)
                        <img src="{{ asset('storage/'.$nurse->photo) }}"
                             class="w-24 h-24 rounded-full object-cover border-2 border-blue-500"
                             alt="صورة الممرضة">
                    @else
                        <img src="https://via.placeholder.com/100"
                             class="w-24 h-24 rounded-full object-cover border-2 border-blue-500"
                             alt="صورة الممرضة">
                    @endif
                </div>

                {{-- بيانات أساسية --}}
                <h2 class="text-lg font-bold text-center text-gray-800">{{ $nurse->user->name ?? '---' }}</h2>
                <p class="text-center text-gray-500 mb-2">الجنس: {{ $nurse->gender }}</p>
                <p class="text-center text-green-600 font-semibold mb-2">الراتب: {{ number_format($nurse->salary, 0) }} ل.س</p>
                <p class="text-center text-sm text-gray-400">تاريخ التعيين: {{ $nurse->date_of_appointment }}</p>

                {{-- الخدمات --}}
                <div class="mt-4">
                    <h3 class="text-sm font-bold text-blue-600 mb-2">الخدمات:</h3>
                    @if($nurse->services->count() > 0)
                        <ul class="list-disc list-inside text-sm text-gray-700">
                            @foreach($nurse->services as $service)
                                <li>{{ $service->name }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-400">لا توجد خدمات</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
