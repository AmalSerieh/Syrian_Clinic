

<form action="{{ route('doctor.medical-record.medications.store', $patient->id) }}" method="POST">
    @csrf

    {{-- اسم الدواء --}}
    <div>
        <label>اسم الدواء</label>
        <input type="text" name="med_name" class="form-control" required value="{{ old('med_name') }}">
    </div>

    {{-- نوع الدواء --}}
    <div>
        <label>نوع الدواء</label>
        <select name="med_type" class="form-control" required>
            <option value="current" {{ old('med_type') == 'current' ? 'selected' : '' }}>مؤقت</option>
            <option value="chronic" {{ old('med_type') == 'chronic' ? 'selected' : '' }}>مزمن</option>
        </select>
    </div>

    {{-- تاريخ البداية --}}
    <div>
        <label>تاريخ البدء</label>
        <input type="date" name="med_start_date" class="form-control" required value="{{ old('med_start_date') }}">
    </div>

    {{-- تاريخ النهاية (اختياري) --}}
    <div>
        <label>تاريخ الانتهاء</label>
        <input type="date" name="med_end_date" class="form-control" value="{{ old('med_end_date') }}">
    </div>

    {{-- التكرار --}}
    <div>
        <label>عدد الجرعات</label>
        <select name="med_frequency" class="form-control" required>
            @foreach(['once_daily', 'twice_daily', 'three_times_daily', 'daily', 'weekly', 'monthly', 'yearly'] as $freq)
                <option value="{{ $freq }}" {{ old('med_frequency') == $freq ? 'selected' : '' }}>
                    {{ $freq}}
                </option>
            @endforeach
        </select>
    </div>

    {{-- شكل الدواء --}}
    <div>
        <label>شكل الدواء</label>
        <select name="med_dosage_form" class="form-control" required>
            @foreach(['tablet', 'capsule', 'pills', 'syrup', 'liquid', 'drops', 'sprays', 'patches', 'injections', 'powder'] as $form)
                <option value="{{ $form }}" {{ old('med_dosage_form') == $form ? 'selected' : '' }}>
                    {{ $form }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- كمية الجرعة --}}
    <div>
        <label>كمية الجرعة</label>
        <input type="number" name="med_dose" class="form-control" step="0.1" min="0.1" max="1000" required value="{{ old('med_dose') }}">
    </div>

    {{-- توقيت الجرعة --}}
    <div>
        <label>توقيت الدواء</label>
        <select name="med_timing" class="form-control">
            <option value="">-- بدون توقيت محدد --</option>
            @foreach(['before_food', 'after_food', 'morning', 'evening', 'morning_evening'] as $timing)
                <option value="{{ $timing }}" {{ old('med_timing') == $timing ? 'selected' : '' }}>
                    {{ $timing }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- اسم الطبيب (اختياري) --}}
    <div>
        <label>اسم الطبيب المعالج</label>
        <input type="text" name="med_prescribed_by_doctor" class="form-control" value="{{ old('med_prescribed_by_doctor', auth()->user()->full_name) }}">
    </div>

    <button type="submit" class="btn btn-primary mt-3">حفظ الدواء</button>
</form>
