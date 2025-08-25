<div class="container mx-auto px-6 py-6">

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            🩺 ملف الأدوية للمريض: {{ $patient->name }}
        </h2>
        {{-- زر إضافة دواء دائماً موجود --}}
        <a href="{{ route('doctor.medical-record.medications.create', $patient->id) }}"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            ➕ إضافة دواء
        </a>
    </div>

    {{-- إذا لا يوجد أي دواء --}}
    @if ($current->isEmpty() && $chronic->isEmpty())
        <div class="text-center py-10">
            <p class="text-gray-500 mb-4">لا توجد أدوية مسجلة لهذا المريض.</p>
            <a href="{{ route('doctor.medical-record.medications.create', $patient->id) }}"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                ➕ إضافة أول دواء
            </a>
        </div>
    @else
        {{-- ✅ الأدوية الحالية --}}
       <div class="mb-10">
    <h3 class="text-xl font-semibold text-blue-600 mb-4">💊 الأدوية الحالية</h3>

    @if($current->isEmpty())
        <p class="text-gray-500">لا توجد أدوية حالية.</p>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            @foreach($current as $med)
                <div class="bg-white shadow-md rounded-xl p-4">
                    <h4 class="font-bold text-lg mb-2">{{ $med['med_name'] }}</h4>

                    <div class="grid grid-cols-2 gap-2 text-gray-700 text-sm">
                        <p><strong>الجرعة:</strong> {{ $med['dose'] }} × {{ $med['quantity_per_dose'] }} {{ $med['dosage_form'] }}</p>
                        <p><strong>التكرار:</strong> {{ $med['frequency'] }} / {{ $med['med_frequency_value'] }}</p>
                        <p><strong>بداية العلاج:</strong> {{ $med['start_date'] }}</p>
                        <p><strong>نهاية العلاج:</strong> {{ $med['end_date'] ?? '—' }}</p>
                        <p><strong>توقيت العلاج:</strong> {{ $med['timing'] }}</p>
                        <p><strong>العدد الكلي المتوقع:</strong> {{ $med['med_total_quantity'] }}</p>
                        <p><strong>الطبيب الموصي:</strong> Dr.{{ $med['med_prescribed_by_doctor'] }}</p>
                    </div>

                    {{-- Progress bar --}}
                    @if($med['progress_percent % '] !== null)
                        <div class="mt-3">
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full" style="width: {{ $med['progress_percent % '] }}%"></div>
                            </div>
                            <small class="text-gray-600">{{ $med['progress_percent % '] }}% مكتمل</small>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>


        {{-- ✅ الأدوية المزمنة --}}
        <div>
            <h3 class="text-xl font-semibold text-red-600 mb-4">الأدوية المزمنة</h3>
            @if ($chronic->isEmpty())
                <p class="text-gray-500">لا توجد أدوية مزمنة.</p>
            @else
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($chronic as $med)
                        <div class="bg-white shadow-md rounded-xl p-4">
                            <h4 class="font-bold text-lg mb-2">{{ $med['med_name'] }}</h4>
                            <p><strong>الجرعة:</strong> {{ $med['dose'] }} ({{ $med['quantity_per_dose'] }} ×
                                {{ $med['dosage_form'] }})</p>
                            <p><strong>عدد المرات:</strong> {{ $med['frequency'] }} /
                                {{ $med['med_frequency_value'] }}</p>
                            <p><strong>بداية العلاج:</strong> {{ $med['start_date'] }}</p>
                            <p><strong>مستمر حتى:</strong> {{ $med['end_date'] ?? 'غير محدد' }}</p>
                            <p><strong>الطبيب الموصي:</strong> {{ $med['med_prescribed_by_doctor'] }}</p>

                            {{-- Progress info --}}
                            <div class="mt-3">
                                <p><strong>استهلك حتى الآن:</strong> {{ $med['taken_till_now'] }} /
                                    {{ $med['med_total_quantity'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>
