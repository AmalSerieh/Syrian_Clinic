@extends('layouts.app')
@section('content')
<div class="container">
    <h2>تعديل الملف الطبي</h2>

    <form action="{{ route('doctor.medical-record.medicalFiles.update', $medicalFile->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>اسم التحليل</label>
            <input type="text" name="test_name" class="form-control" value="{{ $medicalFile->test_name }}" required>
        </div>

        <div class="form-group">
            <label>اسم المختبر</label>
            <input type="text" name="test_laboratory" class="form-control" value="{{ $medicalFile->test_laboratory }}" required>
        </div>

        <div class="form-group">
            <label>تاريخ التحليل</label>
            <input type="date" name="test_date" class="form-control" value="{{ $medicalFile->test_date }}" required>
        </div>

        <div class="form-group">
            <label>الملف الحالي:</label><br>
            @if($medicalFile->test_image_pdf)
                <a href="{{ asset('storage/' . $medicalFile->test_image_pdf) }}" target="_blank">عرض الملف</a>
            @else
                لا يوجد ملف مرفوع
            @endif
        </div>

        <div class="form-group">
            <label>استبدال الملف (اختياري)</label>
            <input type="file" name="test_image_pdf" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">💾 حفظ التعديلات</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">رجوع</a>
    </form>
</div>
@endsection
