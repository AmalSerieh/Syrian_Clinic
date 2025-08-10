<!-- resources/views/patient/diseases/index.blade.php -->

@foreach ($diseases['current'] as $disease)
    <div class="disease-box">
        <strong>النوع:</strong> {{ $disease->d_type }}<br>
        <strong>الاسم:</strong> {{ $disease->d_name }}<br>
        <strong>تاريخ التشخيص:</strong> {{ $disease->d_diagnosis_date }}<br>
        <strong>الطبيب المشخّص:</strong> {{ $disease->d_doctor }}<br>
        <strong>نصائح:</strong> {{ $disease->d_advice }}<br>
        <strong>محظورات:</strong> {{ $disease->d_prohibitions }}<br>

        <!-- زر تعديل -->
        <a href="{{ route('doctor.medical-record.diseases.edit', $disease->id) }}" class="btn btn-sm btn-primary">✏️
            تعديل</a>

        <!-- زر حذف -->
        <form action="{{ route('doctor.medical-record.diseases.delete', $disease->id) }}" method="POST"
            style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">🗑️ حذف</button>
        </form>
    </div>
@endforeach
<br> <br> <br>
@foreach ($diseases['chronic'] as $disease)
    <div class="disease-box">
        <strong>النوع:</strong> {{ $disease->d_type }}<br>
        <strong>الاسم:</strong> {{ $disease->d_name }}<br>
        <strong>تاريخ التشخيص:</strong> {{ $disease->d_diagnosis_date }}<br>
        <strong>الطبيب المشخّص:</strong> {{ $disease->d_doctor }}<br>
        <strong>نصائح:</strong> {{ $disease->d_advice }}<br>
        <strong>محظورات:</strong> {{ $disease->d_prohibitions }}<br>

        <!-- زر تعديل -->
        <a href="{{ route('doctor.medical-record.diseases.edit', $disease->id) }}" class="btn btn-sm btn-primary">✏️
            تعديل</a>

        <!-- زر حذف -->
        <form action="{{ route('doctor.medical-record.diseases.delete', $disease->id) }}" method="POST"
            style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">🗑️ حذف</button>
        </form>
    </div>
@endforeach
