<?php

namespace App\Services\Api\PateintRecord;

use App\Models\Medication;
use App\Repositories\Api\PateintRecord\MedicationRepositoryInterface;
use Illuminate\Support\Carbon;

class MedicationService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected MedicationRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }
    private function getFrequencyValue(string $code): float
    {
        return match ($code) {
            'once_daily', 'daily' => 1,
            'twice_daily' => 2,
            'three_times_daily' => 3,
            'twice_weekly' => 2 / 7,
            'weekly' => 1 / 7,
            'monthly' => 1 / 30,
            'yearly' => 1 / 365,
            default => 1, // قيمة افتراضية إذا دخلت قيمة غير معروفة
        };
    }
    private function getFrequencyInfo(string $code): array
{
    return match ($code) {
        'once_daily', 'daily' => ['value' => 1, 'type' => 'daily'],
        'twice_daily' => ['value' => 2, 'type' => 'daily'],
        'three_times_daily' => ['value' => 3, 'type' => 'daily'],
        'twice_weekly' => ['value' => 2, 'type' => 'weekly'],
        'weekly' => ['value' => 1, 'type' => 'weekly'],
        'monthly' => ['value' => 1, 'type' => 'monthly'],
        'yearly' => ['value' => 1, 'type' => 'yearly'],
        default => ['value' => 1, 'type' => 'daily'],
    };
}


   public function calculateTotalQuantity(array $data): int
{
    $start = Carbon::parse($data['med_start_date']);
    $now = now();

    if ($start->gt($now)) {
        // إذا كان الدواء يبدأ في المستقبل، لا يوجد كمية بعد
        return 0;
    }

    // إذا كان الدواء مؤقت وله end date
    if ($data['med_type'] === 'current' && !empty($data['med_end_date'])) {
        $end = Carbon::parse($data['med_end_date']);
    } else {
        // chronic → بدون end_date، نحسب حتى الآن فقط
        $end = $now;
    }

    $days = $start->diffInDays($end) + 1;
    $perDay = $this->getFrequencyValue($data['med_frequency']);
    $quantityPerDose = (float) $data['med_dose'];

    return (int) ceil($days * $perDay * $quantityPerDose);
}


    public function create(array $data, int $recordId)
    {
        $data['patient_record_id'] = $recordId;
        $data['med_frequency_value'] = $this->getFrequencyValue($data['med_frequency']);

        if ($data['med_type'] === 'chronic') {
            $data['med_end_date'] = null;
        }
        // اجعل quantity_per_dose = med_dose
        $data['med_quantity_per_dose'] = $data['med_dose'];

        $data['med_total_quantity'] = $this->calculateTotalQuantity($data);

        return $this->repo->create($data);
    }

    /**
     * تحديث med_taken_quantity في قاعدة البيانات بناءً على الحساب الحي.
     */
    public function updateTakenQuantity(Medication $medication): void
    {
        $taken = $medication->calculateTakenQuantity();
        $medication->med_taken_quantity = $taken;
         if ($medication->med_type === 'chronic') {
                $medication->med_total_quantity = $taken;
            }
        $medication->save();
    }
     /**
     * تحديث كل الأدوية دفعة واحدة.
     */
    public function updateAllMedications(): void
    {
        Medication::chunk(100, function ($medications) {
            foreach ($medications as $medication) {
                $this->updateTakenQuantity($medication);
            }
        });
    }
}
