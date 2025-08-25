<?php

namespace App\Traits\Medication;

use Illuminate\Support\Carbon;

trait HasMedication
{
    public function getIsActiveAttribute()
    {
        if ($this->med_type === 'chronic') {
            return true;
        }
        if ($this->med_end_date && now()->lte($this->med_end_date)) {
            return true;
        }
        return false;
    }


    /**
     * حساب الكمية التي "كان المفروض" أن يأخذها المريض حتى الآن.
     */
    public function calculateTakenQuantity(): float
    {
        if (!$this->med_start_date) {
            return 0;
        }

        $start = Carbon::parse($this->med_start_date)->startOfDay();
        $now = Carbon::now();

        // إذا لم يبدأ بعد
        if ($now->lt($start)) {
            return 0;
        }

        // نهاية الفترة المؤقتة أو الآن
        if ($this->med_type === 'current' && $this->med_end_date) {
            $end = Carbon::parse($this->med_end_date)->endOfDay();
            $periodEnd = $now->gt($end) ? $end : $now;
        } else {
            $periodEnd = $now;
        }

        // الأيام المكتملة قبل اليوم الحالي
        $daysPassed = $start->diffInDays($periodEnd);
        $frequencyValue = floatval($this->med_frequency_value);   // مرات باليوم
        $quantityPerDose = floatval($this->med_quantity_per_dose ?? $this->med_dose);

        $completedDoses = $daysPassed * $frequencyValue * $quantityPerDose;

        // --- حساب جزئي لليوم الحالي ---
        $secondsInDay = 24 * 60 * 60;
        $secondsPassedToday = $now->diffInSeconds($now->copy()->startOfDay());
        $dayProgress = $secondsPassedToday / $secondsInDay;

        $todayExpected = $dayProgress * $frequencyValue * $quantityPerDose;

        $totalTaken = $completedDoses + $todayExpected;

        // ما يزيد عن الكمية الكلية
        $totalQuantity = floatval($this->med_total_quantity);
        $totalTaken = min($totalTaken, $totalQuantity);

        // solid vs liquid
        $solidForms = ['tablet', 'capsule', 'pills'];
        $dosageForm = strtolower($this->med_dosage_form);

        if (in_array($dosageForm, $solidForms)) {
            return intval(round($totalTaken));
        }

        return round($totalTaken, 2);
    }

    public function calculateProgressDetailed5()
    {
        if ($this->med_type !== 'current' || !$this->med_end_date || !$this->med_start_date) {
            return [
                'dose_progress_by 100%' => null,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        $start = \Carbon\Carbon::parse($this->med_start_date);
        $end = \Carbon\Carbon::parse($this->med_end_date);
        $now = \Carbon\Carbon::now();

        if ($now->lt($start)) {
            return [
                'dose_progress_by 100%' => 0,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        if ($now->gt($end)) {
            return [
                'dose_progress_by 100%' => 100,
                'taken_till_now' => floatval($this->med_total_quantity),
                'unit' => $this->resolveUnit(),
            ];
        }

        $totalDays = $start->diffInDays($end) + 1;
        $passedDays = $start->diffInDays($now) + 1;
        $frequencyValue = floatval($this->med_frequency_value);



        $takenTillNow = $passedDays * $frequencyValue;
        $doseQuantity = floatval($this->med_quantity_per_dose);
        $quantityTaken = $takenTillNow * $doseQuantity;


        $totalQuantity = floatval($this->med_total_quantity);

        if ($totalQuantity == 0) {
            $progress = 0;
        } else {
            $progress = min(100, ($quantityTaken / $totalQuantity) * 100);
        }

        // ⚡ هنا نتحقق من النوع:
        $solidForms = ['tablet', 'capsule', 'pills'];

        $dosageForm = strtolower($this->med_dosage_form); // نخليها lower case للاحتياط

        if (in_array($dosageForm, $solidForms)) {
            // نرجع العدد كـ integer لأن المريض ياخذ عدد صحيح من الحبات
            $quantityTaken = intval(round($quantityTaken));
        } else {
            // للسوائل نخليها رقم عشري بدقة معقولة
            $quantityTaken = round($quantityTaken, -2);
        }

        return [
            'dose_progress_by 100%' => round($progress, 0),
            'taken_till_now' => $quantityTaken,
            'unit' => $this->resolveUnit(),

        ];
    }

    public function calculateProgressDetailed4()
    {
        if ($this->med_type !== 'current' || !$this->med_end_date || !$this->med_start_date) {
            return [
                'dose_progress_by 100%' => null,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        $start = \Carbon\Carbon::parse($this->med_start_date)->startOfDay();
        $end = \Carbon\Carbon::parse($this->med_end_date)->endOfDay();
        $now = \Carbon\Carbon::now();

        // إذا لم يبدأ العلاج بعد
        if ($now->lt($start)) {
            return [
                'dose_progress_by 100%' => 0,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        // إذا انتهى العلاج
        if ($now->gt($end)) {
            $totalQuantity = floatval($this->med_total_quantity);
            return [
                'dose_progress_by 100%' => 100,
                'taken_till_now' => $totalQuantity,
                'unit' => $this->resolveUnit(),
            ];
        }

        // حساب الجرعات المتوقعة حتى الآن (وليس المستهلكة فعليًا)
        $frequencyValue = floatval($this->med_frequency_value);
        $doseQuantity = floatval($this->med_quantity_per_dose);

        // عدد الأيام الكاملة المنقضية
        $fullDaysPassed = $start->diffInDays($now);

        // اليوم الحالي
        $currentDay = $now->isSameDay($start) ? 1 : $fullDaysPassed + 1;

        // الجرعات المتوقعة حتى الآن
        $expectedDoses = $currentDay * $frequencyValue;
        $quantityExpected = $expectedDoses * $doseQuantity;

        $totalQuantity = floatval($this->med_total_quantity);
        $progress = $totalQuantity > 0 ? min(100, ($quantityExpected / $totalQuantity) * 100) : 0;

        // ⚡ هنا نتحقق من النوع:
        $solidForms = ['tablet', 'capsule', 'pills'];
        $dosageForm = strtolower($this->med_dosage_form);

        if (in_array($dosageForm, $solidForms)) {
            $quantityExpected = intval(round($quantityExpected));
        } else {
            $quantityExpected = round($quantityExpected, 2);
        }

        return [
            'dose_progress_by 100%' => round($progress, 0),
            'taken_till_now' => $quantityExpected, // هذه هي الجرعات المتوقعة، وليست المستهلكة فعليًا
            'unit' => $this->resolveUnit(),
        ];
    }
    public function calculateProgressDetailed3()
    {
        $start = Carbon::parse($this->med_start_date);
        $now = Carbon::now();
        $totalQuantity = floatval($this->med_total_quantity);
        $doseQuantity = floatval($this->med_quantity_per_dose);
        $frequencyValue = floatval($this->med_frequency_value);

        // لو لم يبدأ الدواء بعد
        if ($now->lt($start)) {
            return [
                'dose_progress_by 100%' => 0,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        // تحديد آخر يوم للدواء
        $end = $this->med_end_date ? Carbon::parse($this->med_end_date) : $now;
        $periodEnd = $now->lt($end) ? $now : $end;

        // حساب عدد الأيام التي مضت فعلياً
        $daysPassed = $start->diffInDays($periodEnd) + 1;

        // كمية مأخوذة حتى الآن
        $quantityTaken = $daysPassed * $frequencyValue * $doseQuantity;

        // لا تتجاوز الكمية الإجمالية
        if ($totalQuantity > 0 && $quantityTaken > $totalQuantity) {
            $quantityTaken = $totalQuantity;
        }

        // حساب النسبة المئوية
        $progress = $totalQuantity > 0 ? round(($quantityTaken / $totalQuantity) * 100, 2) : 0;

        // ضبط النوعية للدواء (حبات أو سائل)
        $solidForms = ['tablet', 'capsule', 'pills'];
        $dosageForm = strtolower($this->med_dosage_form);

        if (in_array($dosageForm, $solidForms)) {
            $quantityTaken = intval(round($quantityTaken));
        } else {
            $quantityTaken = round($quantityTaken, 2);
        }

        return [
            'dose_progress_by 100%' => $progress,
            'taken_till_now' => $quantityTaken,
            'unit' => $this->resolveUnit(),
        ];
    }
    public function calculateProgressDetailed2()
    {
        $start = Carbon::parse($this->med_start_date)->startOfDay();
        $end = $this->med_end_date ? Carbon::parse($this->med_end_date)->endOfDay() : null;
        $now = Carbon::now();

        // إذا لم يبدأ العلاج بعد
        if ($now->lt($start)) {
            return [
                'dose_progress_by_100%' => 0,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        // تحديد نهاية الفترة للحساب
        $periodEnd = $end && $now->gt($end) ? $end : $now;

        // عدد الأيام التي مرت ضمن الفترة
        $daysPassed = $start->diffInDays($periodEnd) + 1;

        // حساب الجرعات الفعلية المتوقعة
        $frequencyValue = floatval($this->med_frequency_value);
        $doseQuantity = floatval($this->med_quantity_per_dose);

        $quantityTaken = $daysPassed * $frequencyValue * $doseQuantity;

        // لا تتجاوز إجمالي كمية الدواء
        $totalQuantity = floatval($this->med_total_quantity);
        if ($quantityTaken > $totalQuantity) {
            $quantityTaken = $totalQuantity;
        }

        // نسبة التقدم %
        $progress = $totalQuantity > 0 ? round(($quantityTaken / $totalQuantity) * 100, 2) : 0;

        // ضبط القيم حسب شكل الدواء
        $solidForms = ['tablet', 'capsule', 'pills'];
        $dosageForm = strtolower($this->med_dosage_form);
        if (in_array($dosageForm, $solidForms)) {
            $quantityTaken = intval(round($quantityTaken));
        } else {
            $quantityTaken = round($quantityTaken, 2);
        }

        return [
            'dose_progress_by_100%' => $progress,
            'taken_till_now' => $quantityTaken,
            'unit' => $this->resolveUnit(),
        ];
    }
    public function calculateProgressDetailed1()
    {
        if (!$this->med_start_date || !$this->med_end_date || $this->med_type !== 'current') {
            return [
                'dose_progress_by_100%' => null,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        $start = \Carbon\Carbon::parse($this->med_start_date)->startOfDay();
        $end = \Carbon\Carbon::parse($this->med_end_date)->endOfDay();
        $now = \Carbon\Carbon::now();

        // إذا لم يبدأ العلاج بعد
        if ($now->lt($start)) {
            return [
                'dose_progress_by_100%' => 0,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        // إذا انتهى العلاج
        if ($now->gt($end)) {
            return [
                'dose_progress_by_100%' => 100,
                'taken_till_now' => floatval($this->med_total_quantity),
                'unit' => $this->resolveUnit(),
            ];
        }

        $frequencyValue = floatval($this->med_frequency_value);
        $doseQuantity = floatval($this->med_quantity_per_dose);

        // عدد الأيام المنقضية منذ البداية (بدون اليوم الحالي)
        $daysPassed = $start->diffInDays($now);

        // اليوم الحالي: نحسب فقط نصف الجرعات أو جرعات اليوم حسب حاجتك
        // هنا نفترض أخذ **جرعات كاملة اليوم** بعد بداية اليوم
        $quantityTaken = ($daysPassed * $frequencyValue * $doseQuantity) + ($frequencyValue * $doseQuantity);

        // لا تتجاوز الكمية الإجمالية
        $totalQuantity = floatval($this->med_total_quantity);
        $quantityTaken = min($quantityTaken, $totalQuantity);

        // نسبة التقدم
        $progress = $totalQuantity > 0 ? ($quantityTaken / $totalQuantity) * 100 : 0;

        // تحويل الأشكال الصلبة إلى integer
        $solidForms = ['tablet', 'capsule', 'pills'];
        $dosageForm = strtolower($this->med_dosage_form);

        if (in_array($dosageForm, $solidForms)) {
            $quantityTaken = intval(round($quantityTaken));
        } else {
            $quantityTaken = round($quantityTaken, 2);
        }

        return [
            'dose_progress_by_100%' => round($progress, 0),
            'taken_till_now' => $quantityTaken,
            'unit' => $this->resolveUnit(),
        ];
    }
    public function calculateProgressDetailed0()
    {
        if (!$this->med_start_date || !$this->med_end_date || $this->med_type !== 'current') {
            return [
                'dose_progress_by_100%' => null,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        $start = \Carbon\Carbon::parse($this->med_start_date)->startOfDay();
        $end = \Carbon\Carbon::parse($this->med_end_date)->endOfDay();
        $now = \Carbon\Carbon::now();

        // إذا لم يبدأ العلاج بعد
        if ($now->lt($start)) {
            return [
                'dose_progress_by_100%' => 0,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        // إذا انتهى العلاج
        if ($now->gt($end)) {
            return [
                'dose_progress_by_100%' => 100,
                'taken_till_now' => floatval($this->med_total_quantity),
                'unit' => $this->resolveUnit(),
            ];
        }

        $frequencyValue = floatval($this->med_frequency_value);   // مرات باليوم
        $doseQuantity = floatval($this->med_quantity_per_dose);

        // الأيام المكتملة قبل اليوم الحالي
        $daysPassed = $start->diffInDays($now);
        $completedDoses = $daysPassed * $frequencyValue * $doseQuantity;

        // --- نحسب جرعات اليوم الحالي بشكل جزئي ---
        $secondsInDay = 24 * 60 * 60;
        $secondsPassedToday = $now->diffInSeconds($now->copy()->startOfDay());

        // كم نسبة اليوم الحالي مرّت
        $dayProgress = $secondsPassedToday / $secondsInDay;

        // الجرعات المتوقعة حتى الآن اليوم
        $todayExpected = $dayProgress * $frequencyValue * $doseQuantity;

        // إجمالي المتوقع حتى الآن
        $quantityTaken = $completedDoses + $todayExpected;

        // لا تتجاوز الكمية الكلية
        $totalQuantity = floatval($this->med_total_quantity);
        $quantityTaken = min($quantityTaken, $totalQuantity);

        // النسبة
        $progress = $totalQuantity > 0 ? ($quantityTaken / $totalQuantity) * 100 : 0;

        // تحويل حسب شكل الجرعة
        $solidForms = ['tablet', 'capsule', 'pills'];
        $dosageForm = strtolower($this->med_dosage_form);

        if (in_array($dosageForm, $solidForms)) {
            $quantityTaken = intval(round($quantityTaken));
        } else {
            $quantityTaken = round($quantityTaken, 2);
        }

        return [
            'dose_progress_by_100%' => round($progress, 0),
            'taken_till_now' => $quantityTaken,
            'unit' => $this->resolveUnit(),
        ];
    }

public function calculateProgressDetailed()
{
    $start = $this->med_start_date ? \Carbon\Carbon::parse($this->med_start_date)->startOfDay() : null;
    $now = \Carbon\Carbon::now();

    $frequencyValue = floatval($this->med_frequency_value);   // مرات باليوم
    $doseQuantity   = floatval($this->med_quantity_per_dose);

    // --- Chronic (دواء مزمن) ---
    if ($this->med_type === 'chronic') {
        if (!$start || $now->lt($start)) {
            return [
                'dose_progress_by_100%' => null,
                'taken_till_now' => 0,
                'unit' => $this->resolveUnit(),
            ];
        }

        // الأيام المكتملة
        $daysPassed = $start->diffInDays($now);

        // الجرعات المكتملة
        $completedDoses = $daysPassed * $frequencyValue * $doseQuantity;

        // --- نحسب جرعات اليوم الحالي بشكل جزئي ---
        $secondsInDay = 24 * 60 * 60;
        $secondsPassedToday = $now->diffInSeconds($now->copy()->startOfDay());
        $dayProgress = $secondsPassedToday / $secondsInDay;
        $todayExpected = $dayProgress * $frequencyValue * $doseQuantity;

        $quantityTaken = $completedDoses + $todayExpected;

        // تنسيق حسب شكل الجرعة
        $solidForms = ['tablet', 'capsule', 'pills'];
        $dosageForm = strtolower($this->med_dosage_form);

        if (in_array($dosageForm, $solidForms)) {
            $quantityTaken = intval(round($quantityTaken));
        } else {
            $quantityTaken = round($quantityTaken, 2);
        }

        return [
            'dose_progress_by_100%' => null, // ما في نسبة مئوية لأنه مستمر
            'taken_till_now' => $quantityTaken,
            'unit' => $this->resolveUnit(),
        ];
    }

    // --- Temporary (current) ---
    if (!$this->med_start_date || !$this->med_end_date) {
        return [
            'dose_progress_by_100%' => null,
            'taken_till_now' => 0,
            'unit' => $this->resolveUnit(),
        ];
    }

    $end = \Carbon\Carbon::parse($this->med_end_date)->endOfDay();

    if ($now->lt($start)) {
        return [
            'dose_progress_by_100%' => 0,
            'taken_till_now' => 0,
            'unit' => $this->resolveUnit(),
        ];
    }

    if ($now->gt($end)) {
        return [
            'dose_progress_by_100%' => 100,
            'taken_till_now' => floatval($this->med_total_quantity),
            'unit' => $this->resolveUnit(),
        ];
    }

    // الأيام المكتملة
    $daysPassed = $start->diffInDays($now);
    $completedDoses = $daysPassed * $frequencyValue * $doseQuantity;

    // جرعات اليوم الحالي
    $secondsInDay = 24 * 60 * 60;
    $secondsPassedToday = $now->diffInSeconds($now->copy()->startOfDay());
    $dayProgress = $secondsPassedToday / $secondsInDay;
    $todayExpected = $dayProgress * $frequencyValue * $doseQuantity;

    $quantityTaken = $completedDoses + $todayExpected;

    $totalQuantity = floatval($this->med_total_quantity);
    $quantityTaken = min($quantityTaken, $totalQuantity);

    $progress = $totalQuantity > 0 ? ($quantityTaken / $totalQuantity) * 100 : 0;

    $solidForms = ['tablet', 'capsule', 'pills'];
    $dosageForm = strtolower($this->med_dosage_form);

    if (in_array($dosageForm, $solidForms)) {
        $quantityTaken = intval(round($quantityTaken));
    } else {
        $quantityTaken = round($quantityTaken, 2);
    }

    return [
        'dose_progress_by_100%' => round($progress, 0),
        'taken_till_now' => $quantityTaken,
        'unit' => $this->resolveUnit(),
    ];
}



    protected function resolveUnit()
    {
        $solidForms = ['tablet', 'capsule', 'pills'];
        $liquidForms = ['syrup', 'liquid', 'injections'];
        $dropForms = ['drops'];
        $patchForms = ['patches'];
        $sprayForms = ['sprays'];
        $powderForms = ['powder'];

        $dosageForm = strtolower($this->med_dosage_form);

        if (in_array($dosageForm, $solidForms)) {
            return 'pills';
        } elseif (in_array($dosageForm, $liquidForms)) {
            return 'ml';
        } elseif (in_array($dosageForm, $dropForms)) {
            return 'drops';
        } elseif (in_array($dosageForm, $patchForms)) {
            return 'patch';
        } elseif (in_array($dosageForm, $sprayForms)) {
            return 'spray';
        } elseif (in_array($dosageForm, $powderForms)) {
            return 'g'; // gram
        } else {
            return '';
        }
    }

}
