<?php
namespace App\Services\Api\PateintRecord;

use App\Models\Allergy;
use App\Repositories\Api\PateintRecord\AllergyRepositoryInterface;
use Illuminate\Support\Collection;
class AllergyService
{
    public function __construct(protected AllergyRepositoryInterface $repo)
    {
    }

    public function createMany(array $data, int $recordId): array
    {
        foreach ($data as &$entry) {
            $entry['patient_record_id'] = $recordId;
            $entry['created_at'] = now();
            $entry['updated_at'] = now();
        }

        // إنشاء العناصر الجديدة واحدة تلو الأخرى (مع استرجاعها)
        $inserted = collect();

        foreach ($data as $entry) {
            $inserted->push($this->repo->create($entry)); // create() يعيد العنصر المُدخل
        }

        return $inserted->all();
    }
    public function getGroupedByPower(int $recordId): Collection
    {
        return $this->repo->getByPatientRecordGroupedByPower($recordId);
    }
    public function create(array $data): Allergy
    {
        return $this->repo->create($data);
    }

    public function update(Allergy $allergy, array $data): Allergy
    {
        $allergy->update($data);
        return $allergy;
    }

    public function delete(Allergy $allergy): void
    {
        $allergy->delete();
    }

}
