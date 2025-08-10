@extends('layouts.secretary.header')

@section('content')
<div class="container">
    <h2>➕ إضافة مورد</h2>
    <form method="POST" action="{{ route('secretary.supplier.store') }}">
        @csrf

        <div class="mb-3">
            <label for="sup_name" class="form-label">اسم المورد</label>
            <input type="text" name="sup_name" class="form-control text-cyan-950" required>
        </div>

        <div class="mb-3">
            <label for="sup_phone" class="form-label">رقم الهاتف</label>
            <input type="text" name="sup_phone" class="form-control text-cyan-950">
        </div>

        <button type="submit" class="btn btn-primary">إضافة</button>
    </form>
</div>
@endsection
