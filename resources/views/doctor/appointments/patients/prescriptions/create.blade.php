@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-md">

    <h2 class="text-2xl font-bold mb-4">إضافة دواء للوصفة</h2>

    <form method="POST" action="{{ route('prescriptions.addMedicine', $prescription->id) }}" class="space-y-4">
        @csrf

        <div>
            <label class="block font-semibold mb-1">نوع الدواء</label>
            <select name="per_type" required class="w-full border border-gray-300 rounded p-2">
                <option value="current">حالٍ</option>
                <option value="chronic">مزمن</option>
            </select>
        </div>

        <div>
            <label class="block font-semibold mb-1">الاسم العلمي / التجاري</label>
            <input type="text" name="per_name" required class="w-full border border-gray-300 rounded p-2" placeholder="مثال: Aspirin 81">
        </div>

        <div>
            <label class="block font-semibold mb-1">الجرعة</label>
            <input type="number" name="per_dose" required step="0.1" class="w-full border border-gray-300 rounded p-2">
        </div>

        <div>
            <label class="block font-semibold mb-1">شكل الجرعة</label>
            <select name="per_dosage_form" required class="w-full border border-gray-300 rounded p-2">
                <option value="tablet">Tablet</option>
                <option value="capsule">Capsule</option>
                <option value="pills">Pills</option>
                <option value="syrup">Syrup</option>
                <option value="injections">Injections</option>
                <!-- Add more as needed -->
            </select>
        </div>

        <div>
            <label class="block font-semibold mb-1">التكرار</label>
            <select name="per_frequency" required class="w-full border border-gray-300 rounded p-2">
                <option value="once_daily">مرة يومياً</option>
                <option value="twice_daily">مرتين يومياً</option>
                <option value="three_times_daily">ثلاث مرات يومياً</option>
                <option value="daily">يومي</option>
                <option value="weekly">أسبوعي</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">تاريخ البداية</label>
                <input type="date" name="per_start_date" required class="w-full border border-gray-300 rounded p-2">
            </div>
            <div>
                <label class="block font-semibold mb-1">تاريخ الانتهاء</label>
                <input type="date" name="per_end_date" class="w-full border border-gray-300 rounded p-2">
            </div>
        </div>

        <div>
            <label class="block font-semibold mb-1">وقت الاستخدام</label>
            <select name="per_timing" class="w-full border border-gray-300 rounded p-2">
                <option value="">غير محدد</option>
                <option value="before_food">قبل الطعام</option>
                <option value="after_food">بعد الطعام</option>
                <option value="morning">صباحاً</option>
                <option value="evening">مساءً</option>
                <option value="morning_evening">صباحاً ومساءً</option>
            </select>
        </div>

        <div>
            <label class="block font-semibold mb-1">تعليمات إضافية</label>
            <textarea name="instructions" rows="3" class="w-full border border-gray-300 rounded p-2"></textarea>
        </div>

        <div>
            <label class="block font-semibold mb-1">البدائل</label>
            <div id="alt-container" class="space-y-2">
                <input type="text" name="pre_alternatives[]" class="alt-input w-full border border-gray-300 rounded p-2" placeholder="بديل 1">
            </div>
            <button type="button" onclick="addAlternative()" class="mt-2 bg-blue-500 text-white px-4 py-1 rounded">+ إضافة بديل</button>
        </div>

        <div>
            <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                💊 إضافة الدواء
            </button>
        </div>
    </form>
</div>

@if($prescription->items->count())
<div class="max-w-5xl mx-auto mt-10 bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">📋 الأدوية المضافة</h2>
    <table class="min-w-full border border-gray-300 text-sm text-right">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2 border">الاسم</th>
                <th class="p-2 border">الجرعة</th>
                <th class="p-2 border">الشكل</th>
                <th class="p-2 border">التكرار</th>
                <th class="p-2 border">البدائل</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prescription->items as $item)
            <tr>
                <td class="p-2 border">{{ $item->pre_name }}</td>
                <td class="p-2 border">{{ $item->pre_dose }}</td>
                <td class="p-2 border">{{ $item->pre_dosage_form }}</td>
                <td class="p-2 border">{{ $item->pre_frequency }}</td>
                <td class="p-2 border">
                    @if($item->pre_alternatives)
                        <ul class="list-disc pr-4">
                            @foreach(json_decode($item->pre_alternatives) as $alt)
                                <li>{{ $alt }}</li>
                            @endforeach
                        </ul>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<script>
    function addAlternative() {
        const container = document.getElementById('alt-container');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'pre_alternatives[]';
        input.placeholder = 'بديل جديد';
        input.classList.add('w-full', 'border', 'border-gray-300', 'rounded', 'p-2', 'alt-input', 'mt-1');
        container.appendChild(input);
    }
</script>
@endsection
