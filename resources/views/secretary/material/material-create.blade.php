<!-- resources/views/secretary/material/material-create.blade.php -->

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">➕ إضافة مادة جديدة</h2>
    </x-slot>

    <div class="p-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('secretary.material.store') }}">
            @csrf

            <div class="mb-3">
                <label for="material_name" class="form-label">اسم المادة</label>
                <input type="text" name="material_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="material_quantity" class="form-label">الكمية</label>
                <input type="number" name="material_quantity" class="form-control" required min="1">
            </div>

            <div class="mb-3">
                <label for="material_price" class="form-label">السعر (ل.س)</label>
                <input type="number" name="material_price" class="form-control" required min="0">
            </div>

            <div class="mb-3">
                <label for="material_location" class="form-label">مكان التخزين</label>
                <input type="text" name="material_location" class="form-control">
            </div>

            <div class="mb-3">
                <label for="material_expiration_date" class="form-label">تاريخ انتهاء الصلاحية</label>
                <input type="date" name="material_expiration_date" class="form-control">
            </div>

            <div class="mb-3">
                <label for="material_threshold" class="form-label">حد التنبيه (كمية منخفضة)</label>
                <input type="number" name="material_threshold" class="form-control" placeholder="مثلاً: 5">
            </div>

            <div class="mb-3">
                <label for="supplier_id" class="form-label">المورد</label>
                <select name="supplier_id" class="form-control" required>
                    <option disabled selected>اختر المورد</option>
                    @foreach(\App\Models\Supplier::all() as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">💾 حفظ</button>
            <a href="{{ route('secretary.material') }}" class="btn btn-secondary">↩ رجوع</a>
        </form>
    </div>
</x-app-layout>
