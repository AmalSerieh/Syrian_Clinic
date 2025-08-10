{{-- ملف: medical-record.blade.php --}}

@section('content')
    <div class="container mt-4">
        <h2>🩺 السجل الطبي للمريض: {{ $patient->name }}</h2>

        <div class="row mt-4 g-3">
            <div class="col-md-3">
                <a href="{{ route('doctor.medical-record.patient_profile', $patient->patient_record->id) }}"
                    class="btn btn-outline-primary w-100">📋 الملف الشخصي</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('doctor.medical-record.diseases', $patient->patient_record->id) }}" class="btn btn-outline-primary w-100">🦠
                    الأمراض</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('doctor.medical-record.operations', $patient->patient_record->id) }}"
                    class="btn btn-outline-primary w-100">🔪 العمليات</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('doctor.medical-record.medications', $patient->patient_record->id) }}"
                    class="btn btn-outline-primary w-100">💊 الأدوية</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('doctor.medical-record.medicalAttachment', $patient->patient_record->id) }}"
                    class="btn btn-outline-primary w-100"> 🧪الفحوصات</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('doctor.medical-record.allergies', $patient->patient_record->id) }}"
                    class="btn btn-outline-primary w-100"> 🌿 الحساسيات</a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('doctor.medical-record.medicalFiles', $patient->patient_record->id) }}"
                    class="btn btn-outline-primary w-100"> 📋الملفات المرفقة </a>
            </div>

        </div>
    </div>
@endsection
