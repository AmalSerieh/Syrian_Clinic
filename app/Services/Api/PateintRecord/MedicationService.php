<?php

namespace App\Services\Api\PateintRecord;

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
        'once_daily', 'daily'       => 1,
        'twice_daily'               => 2,
        'three_times_daily'         => 3,
        'weekly'                    => 1 / 7,
        'monthly'                   => 1 / 30,
        'yearly'                    => 1 / 365,
        default                     => 1, // قيمة افتراضية إذا دخلت قيمة غير معروفة
    };
}

    public function calculateTotalQuantity(array $data): int
    {
        $start = Carbon::parse($data['med_start_date']);
        $end = ($data['med_type'] === 'current' && !empty($data['med_end_date']))
            ? Carbon::parse($data['med_end_date'])
            : now();

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
}
