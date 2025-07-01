<?php
namespace App\Repositories\Api\PateintRecord;
interface MedicalAttachmentRepositoryInterface{
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function find(int $id);
    public function getByPatientRecord(int $recordId);
}
