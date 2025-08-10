<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800 leading-tight">
            ✏️ تعديل الدواء
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded p-6">

                @if ($errors->any())
                    <div class="mb-4 text-red-600">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('doctor.medical-record.medications.update', $medication->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label>🧾 نوع الدواء</label>
                            <select name="med_type" class="form-select w-full" required>
                                <option value="current" @selected($medication->med_type === 'current')>حالي</option>
                                <option value="chronic" @selected($medication->med_type === 'chronic')>مزمن</option>
                            </select>
                        </div>

                        <div>
                            <label>💊 اسم الدواء</label>
                            <input type="text" name="med_name" class="form-input w-full" value="{{ old('med_name', $medication->med_name) }}" required>
                        </div>

                        <div>
                            <label>📅 تاريخ البدء</label>
                            <input type="date" name="med_start_date" class="form-input w-full" value="{{ old('med_start_date', $medication->med_start_date) }}" required>
                        </div>

                        <div>
                            <label>📅 تاريخ الانتهاء</label>
                            <input type="date" name="med_end_date" class="form-input w-full" value="{{ old('med_end_date', $medication->med_end_date) }}">
                        </div>

                        <div>
                            <label>⏱️ عدد المرات</label>
                            <select name="med_frequency" class="form-select w-full" required>
                                @foreach ([
                                    'once_daily' => 'مرة يومياً',
                                    'twice_daily' => 'مرتين يومياً',
                                    'three_times_daily' => 'ثلاث مرات يومياً',
                                    'daily' => 'يوميًا',
                                    'weekly' => 'أسبوعيًا',
                                    'monthly' => 'شهريًا',
                                    'yearly' => 'سنويًا',
                                ] as $key => $label)
                                    <option value="{{ $key }}" @selected($medication->med_frequency === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label>💠 شكل الجرعة</label>
                            <select name="med_dosage_form" class="form-select w-full" required>
                                @foreach ([
                                    'tablet' => 'أقراص',
                                    'capsule' => 'كبسولات',
                                    'pills' => 'حبوب',
                                    'syrup' => 'شراب',
                                    'liquid' => 'سائل',
                                    'drops' => 'قطرات',
                                    'sprays' => 'بخاخ',
                                    'patches' => 'لصقات',
                                    'injections' => 'حقن',
                                    'powder' => 'بودرة',
                                ] as $key => $label)
                                    <option value="{{ $key }}" @selected($medication->med_dosage_form === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label>💉 الجرعة</label>
                            <input type="number" name="med_dose" step="0.1" min="0.1" max="1000" class="form-input w-full" value="{{ old('med_dose', $medication->med_dose) }}" required>
                        </div>

                        <div>
                            <label>⏰ توقيت الدواء</label>
                            <select name="med_timing" class="form-select w-full">
                                <option value="">-- اختياري --</option>
                                @foreach ([
                                    'before_food' => 'قبل الطعام',
                                    'after_food' => 'بعد الطعام',
                                    'morning' => 'صباحاً',
                                    'evening' => 'مساءً',
                                    'morning_evening' => 'صباحاً ومساءً',
                                ] as $key => $label)
                                    <option value="{{ $key }}" @selected($medication->med_timing === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label>👨‍⚕️ وصف من قبل الطبيب</label>
                            <input type="text" name="med_prescribed_by_doctor" class="form-input w-full" value="{{ old('med_prescribed_by_doctor', $medication->med_prescribed_by_doctor) }}">
                        </div>
                    </div>

                    <div class="mt-6 text-end">
                        <x-primary-button>💾 حفظ التعديلات</x-primary-button>
                        <a href="{{ route('doctor.medical-record.medications', $medication->patient_record_id) }}" class="ml-4 text-sm text-gray-600 hover:text-gray-900">↩️ رجوع</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
