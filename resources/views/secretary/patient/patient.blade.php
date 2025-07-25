@extends('layouts.secretary.header')

@section('content')

     <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800">📋 قائمة المرضى</h2>
        </x-slot>

        <div class="p-4 bg-red-800 sm:px-10 rounded shadow">
            <table class="w-full table-auto border border-collapse">
                <thead>
                    <tr class="bg-blue-500 text-gray-900 text-sm uppercase font-bold">
                        <th class="px-4 py-2">الصورة</th>
                        <th class="px-4 py-2">الاسم</th>
                        <th class="px-4 py-2">البريد الإلكتروني</th>
                        <th class="px-4 py-2">رقم الموبايل</th>
                        <th class="px-4 py-2">السجل الطبي</th>
                        <th class="px-4 py-2">عدد المواعيد (مكتملة أو ملغاة)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($patients as $patient)
                        <tr class="text-center border-t">
                            <td class="p-2">
                                @if ($patient['photo'])
                                    <img src="{{ asset('storage/' . $patient['photo']) }}" class="w-12 h-12 rounded-full mx-auto">
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-2">{{ $patient['name'] }}</td>
                            <td class="p-2">{{ $patient['email'] }}</td>
                            <td class="p-2">{{ $patient['phone'] }}</td>
                            <td class="p-2">
                                @if ($patient['record_completed'])
                                    ✅ مكتمل
                                @else
                                    ❌ غير مكتمل
                                @endif
                            </td>
                            <td class="p-2">{{ $patient['appointments_count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-4">لا يوجد مرضى حتى الآن.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

@endsection
