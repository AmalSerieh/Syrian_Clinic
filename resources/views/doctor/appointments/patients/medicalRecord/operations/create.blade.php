<x-app-layout>
    <h2>إضافة عملية جديدة</h2>
    <form method="POST" action="{{ route('doctor.medical-record.operations.store', $patientId) }}">
        @csrf
        <input type="text" name="op_name" placeholder="اسم العملية">
        <input type="text" name="op_doctor_name" placeholder="اسم الطبيب">
        <input type="text" name="op_hospital_name" placeholder="اسم المشفى">
        <input type="date" name="op_date" placeholder="تاريخ العملية">
        <button type="submit">إضافة</button>
    </form>
</x-app-layout>
