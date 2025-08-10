<x-app-layout>
    <x-slot name="header">✏️ تعديل الحساسية</x-slot>

    <form action="{{ route('doctor.medical-record.allergies.update', $allergy->id) }}" method="POST">
        @csrf
        <label>اسم الحساسية:</label>
        <input type="text" name="allergy_name" value="{{ old('allergy_name', $allergy->allergy_name) }}" required><br>

        <label>نوع الحساسية:</label>
        <input type="text" name="allergy_type" value="{{ old('allergy_type', $allergy->allergy_type) }}" required><br>

        <label>الوصف:</label>
        <textarea name="description">{{ old('description', $allergy->description) }}</textarea><br>

        <button type="submit">💾 تحديث</button>
    </form>
</x-app-layout>
