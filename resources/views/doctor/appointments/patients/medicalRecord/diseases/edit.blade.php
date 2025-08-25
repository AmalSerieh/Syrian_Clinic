<form action="{{ route('doctor.medical-record.diseases.update', $diseases->id) }}" method="POST">
    @csrf
    <label>نوع المرض</label>
    <select name="d_type">
        <option value="current" @selected($diseases->d_type === 'current')>حالية</option>
        <option value="chronic" @selected($diseases->d_type === 'chronic')>مزمنة</option>
    </select>

    <label>اسم المرض</label>
    <input type="text" name="d_name" value="{{ $diseases->d_name }}">

    <label>تاريخ التشخيص</label>
    <input type="date" name="d_diagnosis_date" value="{{ $diseases->d_diagnosis_date }}">

    <label>الطبيب</label>
    <input type="text" name="d_doctor" value="{{ $diseases->d_doctor }}">

    <label>نصائح</label>
    <textarea name="d_advice">{{ $diseases->d_advice }}</textarea>

    <label>محظورات</label>
    <textarea name="d_prohibitions">{{ $diseases->d_prohibitions }}</textarea>

    <button type="submit">💾 حفظ التعديلات</button>
</form>
