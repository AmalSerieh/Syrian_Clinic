<x-app-layout>
    <x-slot name="header">➕ إضافة حساسية جديدة</x-slot>

    <form action="{{ route('doctor.medical-record.allergies.store', $patientRecord->id) }}" method="POST">
        @csrf

        <label>اسم الحساسية:</label>
        <input type="text" name="allergy_name" value="{{ old('allergy_name') }}" required><br>

        <label>نوع الحساسية:</label>
        <input type="text" name="allergy_type" value="{{ old('allergy_type') }}" required><br>

        <label>الوصف:</label>
        <textarea name="description">{{ old('description') }}</textarea><br>

        <button type="submit">💾 حفظ</button>
    </form>
</x-app-layout>
