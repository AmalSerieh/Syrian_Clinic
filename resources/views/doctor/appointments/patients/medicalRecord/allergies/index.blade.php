<x-app-layout>
    <x-slot name="header">📋 سجل الحساسية</x-slot>

    <a href="{{ route('doctor.medical-record.allergies.create', $patientRecord->id) }}" class="btn btn-primary mb-3">➕ إضافة حساسية</a>

    <ul>
        @foreach ($allergies as $allergy)
            <li>
                {{ $allergy->allergy_name }} ({{ $allergy->allergy_type }})
                <a href="{{ route('doctor.medical-record.allergies.show', $allergy->id) }}">عرض</a> |
                <a href="{{ route('doctor.medical-record.allergies.edit', $allergy->id) }}">تعديل</a> |
                <form action="{{ route('doctor.medical-record.allergies.delete', $allergy->id) }}" method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('هل أنت متأكد؟')">🗑️ حذف</button>
                </form>
            </li>
        @endforeach
    </ul>
</x-app-layout>
