<?php
namespace App\Services\Api\PateintRecord;

use App\Repositories\Api\PateintRecord\PatientProfileRepositoryInterface;
class PatientProfileService{
  protected $repo;

    public function __construct(PatientProfileRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function create(array $data)
    {
        return $this->repo->store($data);
    }

    public function update(array $data, $id)
    {
        return $this->repo->update($data, $id);
    }
     public function getById(int $id)
    {
        return $this->repo->getByPatientId($id);
    }
}
