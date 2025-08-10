<form action="{{ route('doctor.medical-record.diseases.update',  $disease->id ) }}" method="POST">
    @csrf
    @foreach ($diseases as $index => $disease)
        <input type="hidden" name="diseases[{{ $index }}][id]" value="{{ $disease->id }}">

        <label>نوع المرض</label>
        <select name="diseases[{{ $index }}][d_type]">
            <option value="current" @selected($disease->d_type === 'current')>حالية</option>
            <option value="chronic" @selected($disease->d_type === 'chronic')>مزمنة</option>
        </select>

        <label>اسم المرض</label>
        <input type="text" name="diseases[{{ $index }}][d_name]" value="{{ $disease->d_name }}">

        <label>تاريخ التشخيص</label>
        <input type="date" name="diseases[{{ $index }}][d_diagnosis_date]" value="{{ $disease->d_diagnosis_date }}">

        <label>الطبيب</label>
        <input type="text" name="diseases[{{ $index }}][d_doctor]" value="{{ $disease->d_doctor }}">

        <label>نصائح</label>
        <textarea name="diseases[{{ $index }}][d_advice]">{{ $disease->d_advice }}</textarea>

        <label>محظورات</label>
        <textarea name="diseases[{{ $index }}][d_prohibitions]">{{ $disease->d_prohibitions }}</textarea>

        <hr>
    @endforeach

    <button type="submit">💾 حفظ التعديلات</button>
</form>
