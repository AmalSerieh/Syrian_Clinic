@extends('layouts.doctor.header')

@section('content')
<div class="max-w-5xl mx-auto p-6 bg-white shadow rounded-lg">

    <h2 class="text-2xl font-bold mb-4">📄 وصفات الطبيب في الزيارة الحالية</h2>

    @if($prescriptions->isEmpty())
        <p class="text-gray-500">لا توجد وصفات حالياً.</p>
    @else
        @foreach($prescriptions as $prescription)
            <div class="mb-6 border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold mb-2">وصفة #{{ $prescription->id }} - {{ $prescription->created_at->format('Y-m-d') }}</h3>

                @if($prescription->items->isEmpty())
                    <p class="text-gray-500">لا توجد أدوية في هذه الوصفة.</p>
                @else
                    <table class="w-full text-sm text-right border border-gray-300">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-2 border">اسم الدواء</th>
                                <th class="p-2 border">الجرعة</th>
                                <th class="p-2 border">الشكل</th>
                                <th class="p-2 border">التكرار</th>
                                <th class="p-2 border">البدائل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prescription->items as $item)
                                <tr>
                                    <td class="p-2 border">{{ $item->pre_name }}</td>
                                    <td class="p-2 border">{{ $item->pre_dose }}</td>
                                    <td class="p-2 border">{{ $item->pre_dosage_form }}</td>
                                    <td class="p-2 border">{{ $item->pre_frequency }}</td>
                                    <td class="p-2 border">
                                        @if($item->pre_alternatives)
                                            <ul class="list-disc pr-4">
                                                @foreach(json_decode($item->pre_alternatives) as $alt)
                                                    <li>{{ $alt }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
