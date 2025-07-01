<?php
namespace App\Repositories\Eloquent\Api\PateintRecord;

use App\Models\MedicalAttachment;
use App\Repositories\Api\PateintRecord\MedicalAttachmentRepositoryInterface;
class MedicalAttachmentRepository implements MedicalAttachmentRepositoryInterface{
public function create(array $data)
    {
        return MedicalAttachment::create($data);
    }

    public function update(int $id, array $data)
    {
        return MedicalAttachment::where('id', $id)->update($data);
    }

    public function delete(int $id)
    {
        return MedicalAttachment::destroy($id);
    }

    public function find(int $id)
    {
        return MedicalAttachment::findOrFail($id);
    }
public function getByPatientRecord(int $recordId)
    {
        return MedicalAttachment::where('patient_record_id', $recordId)->get();
    }
}
