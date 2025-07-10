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
     * حساب الكمية التي أخذها المريض حتى الآن.
     */
    public function calculateTakenQuantity(): float
    {
        if (!$this->med_start_date) {
            return 0;
        }

        $start = Carbon::parse($this->med_start_date);
        $now = Carbon::now();

        // إذا لم يبدأ بعد
        if ($now->lt($start)) {
            return 0;
        }

        // نهاية الفترة المؤقتة أو الآن
        if ($this->med_type === 'current' && $this->med_end_date) {
            $end = Carbon::parse($this->med_end_date);
            $periodEnd = $now->gt($end) ? $end : $now;
        } else {
            $periodEnd = $now;
        }

        // عدد الأيام التي مرت
        $days = $start->diffInDays($periodEnd) + 1;
        $perDay = floatval($this->med_frequency_value);
        $quantityPerDose = floatval($this->med_dose);

        return intval(round($days * $perDay * $quantityPerDose, 2));
    }
     public function calculateProgressDetailed()
    {
        if ($this->med_type !== 'current' || !$this->med_end_date || !$this->med_start_date) {
            return [
                'dose_progress_by 100%' => null,
                'taken_till_now' => 0,
                'unit' =>  $this->resolveUnit(),
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


        $progress = min(100, ($quantityTaken / floatval($this->med_total_quantity)) * 100);

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
