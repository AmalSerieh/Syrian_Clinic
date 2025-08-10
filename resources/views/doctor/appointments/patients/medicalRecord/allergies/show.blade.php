<x-app-layout>
    <x-slot name="header">🔍 عرض الحساسية</x-slot>

    <p><strong>اسم الحساسية:</strong> {{ $allergy->allergy_name }}</p>
    <p><strong>نوع الحساسية:</strong> {{ $allergy->allergy_type }}</p>
    <p><strong>الوصف:</strong> {{ $allergy->description }}</p>

    <a href="{{ route('doctor.medical-record.allergies.edit', $allergy->id) }}">✏️ تعديل</a>
</x-app-layout>
