<form action="{{ route('doctor.medical-record.diseases.store', $patient->id) }}" method="POST">
    @csrf
    <label>النوع</label>
    <select name="d_type">
        <option value="current">حالية</option>
        <option value="chronic">مزمنة</option>
    </select>

    <label>اسم المرض</label>
    <input type="text" name="d_name" required>

    <label>تاريخ التشخيص</label>
    <input type="date" name="d_diagnosis_date" required>

    <label>الطبيب المشخّص</label>
    <input type="text" name="d_doctor">

    <label>نصائح</label>
    <textarea name="d_advice"></textarea>

    <label>محظورات</label>
    <textarea name="d_prohibitions"></textarea>

    <button type="submit">💾 حفظ</button>
</form>
