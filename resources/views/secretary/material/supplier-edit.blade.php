<!-- resources/views/secretary/material/supplier-edit.blade.php -->

@extends('layouts.secretary.header')

@section('content')
    <x-slot name="header">
        ✏️ تعديل معلومات المورد
    </x-slot>

    <div class="max-w-2xl mx-auto mt-6 bg-white p-6 rounded shadow">
        <form method="POST" action="{{ route('secretary.supplier.update', $supplier->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="sup_name" class="block font-bold">اسم المورد</label>
                <input type="text" name="sup_name" id="sup_name" class="form-input w-full"
                    value="{{ old('sup_name', $supplier->sup_name) }}" required>
            </div>

            <div class="mb-4">
                <label for="sup_phone" class="block font-bold">رقم الهاتف</label>
                <input type="text" name="sup_phone" id="sup_phone" class="form-input w-full"
                    value="{{ old('sup_phone', $supplier->sup_phone) }}">
            </div>

            <div class="text-right">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    💾 حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
@endsection
