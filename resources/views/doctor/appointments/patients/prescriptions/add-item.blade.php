@extends('layouts.doctor.header')
@section('content')
<div class="max-w-4xl mx-auto bg-gray-500 text-black p-6 rounded-xl shadow-md">

    {{-- عنوان --}}
    <h2 class="text-xl font-semibold mb-4">
        إضافة دواء للوصفة رقم #{{ $prescription->id }}
    </h2>

    {{-- رسائل النجاح / الخطأ --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-600 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-600 rounded">
            <ul>
                @foreach($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- الفورم --}}
    <form method="POST" action="{{ route('doctor.prescription.store', $prescription->id) }}" class="space-y-4">
        @csrf

        {{-- نوع الدواء --}}
        <div>
            <label class="block mb-1">نوع الدواء</label>
            <select name="pre_type" class="w-full rounded text-black p-2" required>
                <option value="chronic">مزمن</option>
                <option value="current">حالي</option>
            </select>
        </div>

        {{-- اسم الدواء --}}
        <div>
            <label class="block mb-1">اسم الدواء</label>
            <input type="text" name="pre_name" class="w-full rounded text-black p-2" required>
        </div>

        {{-- الاسم العلمي --}}
        <div>
            <label class="block mb-1">الاسم العلمي</label>
            <input type="text" name="pre_scientific" class="w-full rounded text-black p-2">
        </div>

        {{-- الاسم التجاري --}}
        <div>
            <label class="block mb-1">الاسم التجاري</label>
            <input type="text" name="pre_trade" class="w-full rounded text-black p-2">
        </div>

        {{-- تواريخ --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block mb-1">تاريخ البداية</label>
                <input type="date" name="pre_start_date" class="w-full rounded text-black p-2" required>
            </div>
            <div>
                <label class="block mb-1">تاريخ الانتهاء</label>
                <input type="date" name="pre_end_date" class="w-full rounded text-black p-2">
            </div>
        </div>

        {{-- التكرار --}}
        <div>
            <label class="block mb-1">التكرار</label>
            <select name="pre_frequency" class="w-full rounded text-black p-2" required>
                <option value="once_daily">مرة يومياً</option>
                <option value="twice_daily">مرتين يومياً</option>
                <option value="three_times_daily">ثلاث مرات يومياً</option>
                <option value="daily">يومياً</option>
                <option value="weekly">أسبوعياً</option>
                <option value="monthly">شهرياً</option>
                <option value="yearly">سنوياً</option>
            </select>
        </div>

        {{-- شكل الجرعة --}}
        <div>
            <label class="block mb-1">شكل الجرعة</label>
            <select name="pre_dosage_form" class="w-full rounded text-black p-2" required>
                <option value="tablet">أقراص</option>
                <option value="capsule">كبسولات</option>
                <option value="pills">حبوب</option>
                <option value="syrup">شراب</option>
                <option value="liquid">سائل</option>
                <option value="drops">قطرات</option>
                <option value="sprays">بخاخ</option>
                <option value="patches">لصقات</option>
                <option value="injections">حقن</option>
                <option value="powder">بودرة</option>
            </select>
        </div>

        {{-- الجرعة --}}
        <div>
            <label class="block mb-1">الجرعة</label>
            <input type="number" name="pre_dose" step="0.1" min="0.1" max="1000" class="w-full rounded text-black p-2" required>
        </div>

        {{-- التوقيت --}}
        <div>
            <label class="block mb-1">توقيت تناول الدواء</label>
            <select name="pre_timing" class="w-full rounded text-black p-2" required>
                <option value="before_food">قبل الأكل</option>
                <option value="after_food">بعد الأكل</option>
                <option value="morning">الصبح</option>
                <option value="evening">المساء</option>
                <option value="morning_evening">صباحاً ومساءً</option>
            </select>
        </div>

        {{-- التعليمات --}}
        <div>
            <label class="block mb-1">تعليمات إضافية</label>
            <textarea name="instructions" class="w-full rounded text-black p-2"></textarea>
        </div>

        {{-- بدائل --}}
        <div>
            <label class="block mb-1">بدائل (افصل بينهم بفواصل)</label>
            <input type="text" name="pre_alternatives[]" class="w-full rounded text-black p-2" placeholder="مثال: دواء A, دواء B">
        </div>

        {{-- زر الحفظ --}}
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded text-white">
                إضافة الدواء
            </button>
        </div>
    </form>

    {{-- عرض الأدوية المضافة --}}
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">الأدوية الحالية</h3>
        @if($prescription->items->count() > 0)
            <ul class="space-y-2">
                @foreach($prescription->items as $item)
                    <li class="bg-gray-800 p-3 rounded">
                        <strong>{{ $item->pre_name }}</strong> - {{ $item->pre_dose }} {{ $item->pre_dosage_form }}
                        ({{ $item->pre_frequency }})
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-400">لا توجد أدوية مضافة بعد.</p>
        @endif
    </div>
</div>
@endsection
