<!-- resources/views/secretary/material/edit.blade.php -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">✏️ تعديل المادة</h2>
    </x-slot>

    <div class="max-w-xl mx-auto p-4 bg-white shadow-md rounded-2xl mt-6">
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('secretary.material.update', $material->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">اسم المادة:</label>
                <input type="text" name="material_name" class="form-input w-full" value="{{ old('material_name', $material->material_name) }}" required>
                @error('material_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">الكمية:</label>
                <input type="number" name="material_quantity" class="form-input w-full" value="{{ old('material_quantity', $material->material_quantity) }}" required>
                @error('material_quantity') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">السعر:</label>
                <input type="number" step="0.01" name="material_price" class="form-input w-full" value="{{ old('material_price', $material->material_price) }}" required>
                @error('material_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">الموقع:</label>
                <input type="text" name="material_location" class="form-input w-full" value="{{ old('material_location', $material->material_location) }}">
                @error('material_location') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">تاريخ الانتهاء:</label>
                <input type="date" name="material_expiration_date" class="form-input w-full" value="{{ old('material_expiration_date', $material->material_expiration_date) }}">
                @error('material_expiration_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-1">الحد الأدنى للتنبيه:</label>
                <input type="number" name="material_threshold" class="form-input w-full" value="{{ old('material_threshold', $material->material_threshold) }}">
                @error('material_threshold') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    💾 حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
