{{-- resources/views/doctor/materials/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">📋 المواد المستهلكة في الزيارة الحالية</h2>

    @if($usedMaterials->isEmpty())
        <p class="text-gray-500">لم يتم استهلاك أي مواد في هذه الزيارة.</p>
    @else
        <table class="w-full border text-sm text-right">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">اسم المادة</th>
                    <th class="p-2 border">الكمية</th>
                    <th class="p-2 border">الجودة</th>
                    <th class="p-2 border">المورد</th>
                    <th class="p-2 border">تاريخ الاستخدام</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usedMaterials as $mat)
                    <tr>
                        <td class="p-2 border">{{ $mat->material->material_name }}</td>
                        <td class="p-2 border">{{ $mat->dm_quantity }}</td>
                        <td class="p-2 border">{{ $mat->dm_quality ?? '-' }}</td>
                        <td class="p-2 border">{{ $mat->supplier?->supplier_name ?? 'غير محدد' }}</td>
                        <td class="p-2 border">{{ $mat->dm_used_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
