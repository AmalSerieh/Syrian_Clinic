@extends('layouts.secretary.header')

@section('content')
    <div class="container">
        <h2>➕ Add Supplier</h2>
        <form method="POST" action="{{ route('secretary.supplier.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="sup_name" class="form-label">Name Supplier</label>
                <input type="text" name="sup_name" class="form-control text-cyan-950" required>
            </div>

            <div class="mb-3">
                <label for="sup_phone" class="form-label">Phone Supplier</label>
                <input type="text" name="sup_phone" class="form-control text-cyan-950">
            </div>
            <div class="mb-3">
                <label for="sup_photo" class="form-label"> Photo Supplier</label>
                <input type="file" name="sup_photo" class="form-control text-cyan-950">
            </div>

            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
@endsection
