@extends('layouts.app')
@section('content')
<div class="container">
    <h2>إضافة ملف طبي</h2>

    <form action="{{ route('doctor.medical-record.medicalFiles.store', $patient->id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>اسم التحليل</label>
            <input type="text" name="test_name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>اسم المختبر</label>
            <input type="text" name="test_laboratory" class="form-control" required>
        </div>

        <div class="form-group">
            <label>تاريخ التحليل</label>
            <input type="date" name="test_date" class="form-control" required>
        </div>

        <div class="form-group">
            <label>ملف التحليل (PDF أو صورة)</label>
            <input type="file" name="test_image_pdf" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">💾 حفظ</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">رجوع</a>
    </form>
</div>
@endsection
