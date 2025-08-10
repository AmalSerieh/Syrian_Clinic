<x-app-layout>
    <h2>تعديل العملية</h2>
    <form method="POST" action="{{ route('doctor.medical-record.operations.update', $operation->id) }}">
        @csrf
        <input type="text" name="op_name" value="{{ $operation->op_name }}">
        <input type="text" name="op_doctor_name" value="{{ $operation->op_doctor_name }}">
        <input type="text" name="op_hospital_name" value="{{ $operation->op_hospital_name }}">
        <input type="date" name="op_date" value="{{ $operation->op_date }}">
        <button type="submit">حفظ التعديلات</button>
    </form>
</x-app-layout>
