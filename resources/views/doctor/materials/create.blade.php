{{-- resources/views/doctor/materials/create.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">📦 تسجيل استهلاك مادة</h2>

    <form id="material-form">
        @csrf
        <input type="hidden" name="doctor_id" value="{{ auth()->user()->doctor->id }}">
        <input type="hidden" name="visit_id" value="{{ $visit->id }}">

        <div class="mb-4">
            <label class="block mb-1 font-semibold">المادة</label>
            <select name="material_id" class="w-full border p-2 rounded" required>
                <option value="">-- اختر المادة --</option>
                @foreach($materials as $material)
                    <option value="{{ $material->id }}">{{ $material->material_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">الكمية المستخدمة</label>
            <input type="number" name="dm_quantity" min="1" class="w-full border p-2 rounded" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">تقييم الجودة (اختياري)</label>
            <input type="number" name="dm_quality" min="1" max="5" class="w-full border p-2 rounded">
        </div>

        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
            💾 حفظ
        </button>
    </form>

    <div id="response" class="mt-4 text-green-600 font-semibold hidden"></div>
</div>

<script>
    document.getElementById('material-form').addEventListener('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        fetch("{{ route('doctor.material.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.message){
                document.getElementById('response').innerText = data.message;
                document.getElementById('response').classList.remove('hidden');
                this.reset();
            }
        })
        .catch(err => {
            alert('حدث خطأ في الحفظ.');
        });
    });
</script>
@endsection
