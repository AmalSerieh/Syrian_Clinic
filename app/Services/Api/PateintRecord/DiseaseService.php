<?php

namespace App\Services\Api\PateintRecord;

use App\Models\Disease;
use App\Repositories\Api\PateintRecord\DiseaseRepositoryInterface;
use Illuminate\Support\Collection;

class DiseaseService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected DiseaseRepositoryInterface $repo)
    {
        //
    }
     public function getGroupedByPower(int $recordId): Collection
    {
        return $this->repo->getByPatientRecordGroupedByPower($recordId);
    }
    public function create(array $data): Disease
    {
        return $this->repo->create($data);
    }

    public function update(Disease $disease, array $data): Disease
    {
        $disease->update($data);
        return $disease;
    }

    public function delete(Disease $disease): void
    {
        $disease->delete();
    }

}
