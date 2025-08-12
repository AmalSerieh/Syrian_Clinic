@extends('layouts.secretary.header')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">📋 قائمة المرضى</h2>
    </x-slot>

    <div class="p-4 bg-white sm:px-10 rounded shadow">
        <table class="w-full table-auto border border-collapse">
            <thead>
                <tr class="bg-blue-500 text-white text-sm uppercase font-bold">
                    <th class="px-4 py-2">الصورة</th>
                    <th class="px-4 py-2">الاسم</th>
                    <th class="px-4 py-2">البريد الإلكتروني</th>
                    <th class="px-4 py-2">رقم الموبايل</th>
                    <th class="px-4 py-2">الإجراء</th>
                    <th class="px-4 py-2">عرض التفاصيل</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    <tr class="text-center border-t text-black">
                        <td class="p-2">
                            @if ( $patient['photo'])
                                <img src="{{ asset('storage/' . $patient['photo']) }}"
                                    class="w-12 h-12 rounded-full mx-auto">
                            @else
                                -
                            @endif
                        </td>
                        <td class="p-2">{{ $patient['name'] }}</td>
                        <td class="p-2">{{ $patient['email'] }}</td>
                        <td class="p-2">{{ $patient['phone'] }}</td>

                        <!-- زر الحذف -->
                        <td class="p-2">
                            <form action="{{ route('secretary.patient.delete', $patient['id']) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المريض نهائياً؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                    حذف
                                </button>
                            </form>
                        </td>

                        <!-- زر عرض التفاصيل -->
                        <td class="p-2">
                            <a href="{{ route('secretary.patient.show', $patient['id']) }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">
                                عرض التفاصيل
                            </a>
                        </td>
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
