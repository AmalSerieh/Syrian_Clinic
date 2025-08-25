<div class="container mx-auto px-6 py-6" x-data="{ edit: false, med: {} }">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            🩺 ملف الأدوية للمريض: {{ $patient->user->name }}
        </h2>
        {{-- زر إضافة دواء --}}
        <a href="{{ route('doctor.medical-record.medications.create', $patient->id) }}"
            class="px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2">
            <span>➕</span> إضافة دواء
        </a>
    </div>

    @if ($current->isEmpty() && $chronic->isEmpty())
        <div class="text-center py-20 bg-yellow-50 rounded-lg border border-yellow-300 shadow-inner">
            <p class="text-gray-600 mb-6 text-lg">لا توجد أدوية مسجلة لهذا المريض.</p>
            <a href="{{ route('doctor.medical-record.medications.create', $patient->id) }}"
                class="inline-block px-7 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                ➕ إضافة أول دواء
            </a>
        </div>
    @else
        {{-- الأدوية الحالية --}}
        <section class="mb-12">
            <h3 class="text-2xl font-semibold text-blue-700 mb-6 border-l-4 border-blue-500 pl-3">💊 الأدوية الحالية
            </h3>

            @if ($current->isEmpty())
                <p class="text-gray-500 italic">لا توجد أدوية حالية.</p>
            @else
                <div class="space-y-6">
                    @foreach ($current as $med)
                        <div
                            class="bg-white rounded-xl shadow-md p-6 group hover:shadow-xl transition-shadow border border-gray-200">
                            <h4 class="text-xl font-bold mb-4 border-b border-gray-300 pb-2 text-gray-800">
                                {{ $med['med_name'] }}
                            </h4>

                            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-gray-700 text-sm">
                                <dt class="font-semibold">الجرعة:</dt>
                                <dd>{{ $med['dose'] }}</dd>

                                <dt class="font-semibold">شكل الدواء:</dt>
                                <dd>{{ $med['dosage_form'] }}</dd>

                                <dt class="font-semibold">كمية الجرعة:</dt>
                                <dd>{{ $med['quantity_per_dose'] }}</dd>

                                <dt class="font-semibold">بداية العلاج:</dt>
                                <dd>{{ $med['start_date'] }}</dd>

                                <dt class="font-semibold">نهاية العلاج:</dt>
                                <dd>{{ $med['end_date'] ?? '—' }}</dd>

                                <dt class="font-semibold">توقيت العلاج:</dt>
                                <dd>{{ $med['timing'] }}</dd>

                                <dt class="font-semibold">التكرار:</dt>
                                <dd>{{ $med['frequency'] }}</dd>

                                <dt class="font-semibold">قيمة التكرار:</dt>
                                <dd>{{ $med['med_frequency_value'] }}</dd>

                                <dt class="font-semibold">العدد الكلي المتوقع:</dt>
                                <dd>{{ $med['med_total_quantity'] }}</dd>

                                <dt class="font-semibold">الطبيب الموصي:</dt>
                                <dd>Dr. {{ $med['med_prescribed_by_doctor'] }}</dd>

                                <dt class="font-semibold">الكمية المأخوذة:</dt>
                                <dd>{{ $med['taken_till_now'] }}</dd>

                                <dt class="font-semibold">التقدم:</dt>
                                <dd>
                                    @if (is_array($med['progress_info']))
                                        نسبة الجرعة: {{ $med['progress_info']['dose_progress_by 100%'] ?? '—' }}<br>
                                        أخذ حتى الآن: {{ $med['progress_info']['taken_till_now'] ?? '—' }}<br>
                                        الوحدة: {{ $med['progress_info']['unit'] ?? '—' }}
                                    @else
                                        {{ $med['progress_info'] }}
                                    @endif
                                </dd>

                                <dt class="font-semibold">التقدم ك نسبة:</dt>
                                <dd>
                                    @if (!empty($med['progress_percent % ']) && !is_array($med['progress_percent % ']))
                                        <div class="mt-2">
                                            <div class="w-full bg-gray-300 rounded-full h-4 overflow-hidden">
                                                <div class="bg-green-500 h-4 rounded-full transition-all duration-500"
                                                    style="width: {{ $med['progress_percent % '] }}%"></div>
                                            </div>
                                            <small class="text-gray-600 font-semibold">
                                                {{ $med['progress_percent % '] }}% مكتمل
                                            </small>
                                        </div>
                                    @else
                                        <span>—</span>
                                    @endif
                                </dd>

                                <dd>
                                    <div class="flex gap-3 mt-3">
                                        <!-- زر تعديل -->
                                        <button @click="edit = true; med = @js($med)"
                                            class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                                            ✏️ تعديل
                                        </button>

                                        <!-- زر حذف -->
                                        <form
                                            action="{{ route('doctor.medical-record.medications.delete', $med['id']) }}"
                                            method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدواء؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                🗑️ حذف
                                            </button>
                                        </form>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- الأدوية المزمنة --}}
        <section>
            <h3 class="text-2xl font-semibold text-red-600 mb-6 border-l-4 border-red-500 pl-3">💊 الأدوية المزمنة</h3>

            @if ($chronic->isEmpty())
                <p class="text-gray-500 italic">لا توجد أدوية مزمنة.</p>
            @else
                <div class="space-y-6">
                    @foreach ($chronic as $med)
                        <div
                            class="bg-white rounded-xl shadow-md p-6 group hover:shadow-xl transition-shadow border border-gray-200">
                            <h4 class="text-xl font-bold mb-4 border-b border-gray-300 pb-2 text-gray-800">
                                {{ $med['med_name'] }}
                            </h4>

                            <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-gray-700 text-sm">
                                <dt class="font-semibold">الجرعة:</dt>
                                <dd>{{ $med['dose'] }}</dd>

                                <dt class="font-semibold">شكل الدواء:</dt>
                                <dd>{{ $med['dosage_form'] }}</dd>

                                <dt class="font-semibold">كمية الجرعة:</dt>
                                <dd>{{ $med['quantity_per_dose'] }}</dd>

                                <dt class="font-semibold">بداية العلاج:</dt>
                                <dd>{{ $med['start_date'] }}</dd>

                                <dt class="font-semibold">نهاية العلاج:</dt>
                                <dd>{{ $med['end_date'] ?? '—' }}</dd>

                                <dt class="font-semibold">توقيت العلاج:</dt>
                                <dd>{{ $med['timing'] }}</dd>

                                <dt class="font-semibold">التكرار:</dt>
                                <dd>{{ $med['frequency'] }}</dd>

                                <dt class="font-semibold">قيمة التكرار:</dt>
                                <dd>{{ $med['med_frequency_value'] }}</dd>

                                <dt class="font-semibold">العدد الكلي المتوقع:</dt>
                                <dd>{{ $med['med_total_quantity'] }}</dd>

                                <dt class="font-semibold">الطبيب الموصي:</dt>
                                <dd>Dr. {{ $med['med_prescribed_by_doctor'] }}</dd>

                                <dt class="font-semibold">الكمية المأخوذة:</dt>
                                <dd>{{ $med['taken_till_now'] }}</dd>

                                <dt class="font-semibold">التقدم:</dt>
                                <dd>
                                    @if (is_array($med['progress_info']))
                                        نسبة الجرعة: {{ $med['progress_info']['dose_progress_by 100%'] ?? '—' }}<br>
                                        أخذ حتى الآن: {{ $med['progress_info']['taken_till_now'] ?? '—' }}<br>
                                        الوحدة: {{ $med['progress_info']['unit'] ?? '—' }}
                                    @else
                                        {{ $med['progress_info'] }}
                                    @endif
                                </dd>

                                <dt class="font-semibold">التقدم ك نسبة:</dt>
                                <dd>
                                    @if (!empty($med['progress_percent % ']) && !is_array($med['progress_percent % ']))
                                        <div class="mt-2">
                                            <div class="w-full bg-gray-300 rounded-full h-4 overflow-hidden">
                                                <div class="bg-red-500 h-4 rounded-full transition-all duration-500"
                                                    style="width: {{ $med['progress_percent % '] }}%"></div>
                                            </div>
                                            <small class="text-gray-600 font-semibold">
                                                {{ $med['progress_percent % '] }}% مكتمل
                                            </small>
                                        </div>
                                    @else
                                        <span>—</span>
                                    @endif
                                </dd>

                                <dd>
                                    <div class="flex gap-3 mt-3">
                                        <!-- زر تعديل -->
                                        <button @click="edit = true; med = @js($med)"
                                            class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                                            ✏️ تعديل
                                        </button>

                                        <!-- زر حذف -->
                                        <form
                                            action="{{ route('doctor.medical-record.medications.delete', $med['id']) }}"
                                            method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدواء؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                🗑️ حذف
                                            </button>
                                        </form>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Modal تعديل دواء (واحد مشترك) -->
        <!-- Modal تعديل دواء -->
        <div x-show="edit" x-transition
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="text-black bg-white rounded-2xl p-6 w-full max-w-lg relative">
                <button @click="edit = false" class="absolute top-3 right-3 text-gray-700 text-xl">&times;</button>
                <h3 class="text-xl font-bold mb-4">✏️ تعديل دواء</h3>

                <form :action="'/doctor/medical-record/' + med.id + '/medications/update'" method="POST"
                    class="space-y-4">
                    @csrf
                    <!-- مهم لأنه update -->

                    <div>
                        <label>اسم الدواء</label>
                        <input type="text" name="med_name" x-model="med.med_name" class="w-full border rounded p-2"
                            required>
                    </div>

                    <div>
                        <label>🧾 نوع الدواء</label>
                        <select name="med_type" x-model="med.med_type" class="form-select w-full" required>
                            <option value="current">حالي</option>
                            <option value="chronic">مزمن</option>
                        </select>
                    </div>

                    <div>
                        <label>💉 الجرعة</label>
                        <input type="number" name="med_dose" step="0.1" min="0.1" max="1000"
                            x-model="med.dose" class="form-input w-full" required>
                    </div>

                    <div>
                        <label>💠 شكل الجرعة</label>
                        <select name="med_dosage_form" x-model="med.dosage_form" class="form-select w-full" required>
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

                    <div>
                        <label>⏱️ التكرار</label>
                        <select name="med_frequency" x-model="med.frequency" class="form-select w-full" required>
                            <option value="once_daily">مرة يومياً</option>
                            <option value="twice_daily">مرتين يومياً</option>
                            <option value="three_times_daily">ثلاث مرات يومياً</option>
                            <option value="daily">يوميًا</option>
                            <option value="weekly">أسبوعيًا</option>
                            <option value="monthly">شهريًا</option>
                            <option value="yearly">سنويًا</option>
                        </select>
                    </div>

                    <div>
                        <label>⏰ توقيت الدواء</label>
                        <select name="med_timing" x-model="med.timing" class="form-select w-full">
                            <option value="">-- اختياري --</option>
                            <option value="before_food">قبل الطعام</option>
                            <option value="after_food">بعد الطعام</option>
                            <option value="morning">صباحاً</option>
                            <option value="evening">مساءً</option>
                            <option value="morning_evening">صباحاً ومساءً</option>
                        </select>
                    </div>

                    <div>
                        <label>📅 بداية العلاج</label>
                        <input type="date" name="med_start_date" x-model="med.start_date"
                            class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label>📅 نهاية العلاج</label>
                        <input type="date" name="med_end_date" x-model="med.end_date"
                            class="w-full border rounded p-2">
                    </div>

                    <div>
                        <label>👨‍⚕️ وصف من قبل الطبيب</label>
                        <input type="text" name="med_prescribed_by_doctor" x-model="med.med_prescribed_by_doctor"
                            class="form-input w-full">
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            💾 حفظ التعديلات
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @endif
</div>

<!-- Alpine.js -->
<script src="//unpkg.com/alpinejs" defer></script>
