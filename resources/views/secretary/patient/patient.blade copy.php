@extends('layouts.secretary.header')

@section('content')
    <x-auth-session-status class="mb-8" :status="session('status')" />
    @if (session('error'))
        <div class="p-4 bg-red-600/70 border-l-4 border-red-500 text-white rounded-lg shadow-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                <div>{{ session('error') }}</div>
            </div>
        </div>
    @endif

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">📋 قائمة المرضى</h2>
    </x-slot>

    <div class="p-6 bg-[#0B1622] min-h-screen text-white rounded-3xl">

        <div class="overflow-x-auto rounded-2xl">
            <table class="min-w-full text-sm text-center table-auto">
                <thead class="bg-[#0f2538] text-gray-400">
                    <tr>
                        <th class="p-4">Name</th>
                        <th class="p-4"> Email</th>
                        <th class="p-4"> Phone</th>
                        <th class="p-4">Action</th>
                        <th class="p-4"> Show Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#1b2d42]">
                    @forelse ($patients as $patient)
                        <tr class="transition text-white text-center ">
                            <td class="p-4 flex items-center justify-center gap-3 font-medium">
                                @if ($patient['photo'])
                                    <img src="{{ asset('storage/' . $patient['photo']) }}"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-slate-700 "
                                        alt="صورة المريض">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-slate-700 flex items-center justify-center">
                                        -
                                    </div>
                                @endif
                                {{ $patient['name'] }}
                            </td>
                            <td class="p-4">{{ $patient['email'] }}</td>
                            <td class="p-4">{{ $patient['phone'] }}</td>

                            <!-- زر الحذف -->
                            <td class="p-4">
                                <form action="{{ route('secretary.patient.delete', $patient['id']) }}" method="POST"
                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المريض نهائياً؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded-l-md rounded-r-md text-sm transition">
                                        DELETE
                                    </button>
                                </form>
                            </td>

                            <!-- زر عرض التفاصيل -->
                            <td class="p-4">
                                <a href="{{ route('secretary.patient.show', $patient['id']) }}"
                                    class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-1  rounded-l-md rounded-r-md text-ls transition inline-block   ">
                                    Go to details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-6 text-gray-400">لا يوجد مرضى حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
