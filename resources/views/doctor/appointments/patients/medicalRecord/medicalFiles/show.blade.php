@extends('layouts.app')
@section('content')
<div class="container">
    <h2>الملفات الطبية للمريض: {{ $patient->user->name }}</h2>

    @if($files->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>اسم التحليل</th>
                    <th>المختبر</th>
                    <th>التاريخ</th>
                    <th>الملف</th>
                    <th>خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                    <tr>
                        <td>{{ $file->test_name }}</td>
                        <td>{{ $file->test_laboratory }}</td>
                        <td>{{ $file->test_date }}</td>
                        <td>
                            @if($file->test_image_pdf)
                                <a href="{{ asset('storage/' . $file->test_image_pdf) }}" target="_blank">📄 عرض</a>
                            @else
                                لا يوجد
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('doctor.medical-record.medicalFiles.edit', $file->id) }}" class="btn btn-warning btn-sm">تعديل</a>
                            <form action="{{ route('doctor.medical-record.medicalFiles.delete', $file->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">🗑️ حذف</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('doctor.medical-record.medicalFiles.deleteAll', $patient->patient_record->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger" onclick="return confirm('هل تريد حذف جميع الملفات الطبية؟')">🧹 حذف الكل</button>
        </form>
    @else
        <p>لا توجد ملفات طبية بعد.</p>
    @endif

    <a href="{{ route('doctor.medical-record.medicalFiles.create', $patient->id) }}" class="btn btn-primary mt-3">➕ إضافة ملف طبي جديد</a>
</div>
@endsection
