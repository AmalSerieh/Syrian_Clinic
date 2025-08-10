@extends('layouts.secretary.header')

@section('content')
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight">
            🧾 قائمة الموردين
        </h2>
    </x-slot>

    <div class="py-4 px-6">
        <a href="{{ route('secretary.supplier.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mb-4 inline-block">
            ➕ إضافة مورد جديد
        </a>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">اسم المورد</th>
                        <th class="px-6 py-3">رقم الهاتف</th>
                        <th class="px-6 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr class="border-b">
                            <td class="px-6 py-3">{{ $supplier->id }}</td>
                            <td class="px-6 py-3">{{ $supplier->sup_name }}</td>
                            <td class="px-6 py-3">{{ $supplier->sup_phone ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('secretary.supplier.edit', $supplier->id) }}"
                                    class="text-blue-600 hover:underline">✏️ تعديل</a>

                                <form action="{{ route('secretary.supplier.delete', $supplier->id) }}" method="POST"
                                    class="inline-block ml-2"
                                    onsubmit="return confirmDelete({{ $supplier->supplier_materials_count }});">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">🗑️ حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                لا يوجد موردون حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if ($suppliers->count())
                <form action="{{ route('secretary.supplier.deleteAll') }}" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من حذف جميع الموردين؟');" class="mt-4 text-center">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        🗑️ حذف جميع الموردين
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ✅ تنبيه الحذف الذكي --}}
    <script>
        function confirmDelete(materialCount) {
            if (materialCount > 0) {
                let word = materialCount === 1 ? 'مادة واحدة' : materialCount <= 2 ? 'مادتين' : materialCount + ' مواد';
                return confirm('⚠️ هذا المورد مرتبط بـ ' + word + '. هل أنت متأكد من الحذف؟');
            }
            return confirm('هل أنت متأكد أنك تريد حذف هذا المورد؟');
        }
    </script>
@endsection
