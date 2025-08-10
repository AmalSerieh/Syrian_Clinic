<x-app-layout>
    <h2>تفاصيل العملية</h2>
    <p>اسم العملية: {{ $operation->op_name }}</p>
    <p>اسم الطبيب: {{ $operation->op_doctor_name }}</p>
    <p>المشفى: {{ $operation->op_hospital_name }}</p>
    <p>تاريخ العملية: {{ $operation->op_date }}</p>
</x-app-layout>
