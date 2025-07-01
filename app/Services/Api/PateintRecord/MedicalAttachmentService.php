<?php
namespace App\Services\Api\PateintRecord;

use App\Repositories\Api\PateintRecord\MedicalAttachmentRepositoryInterface;
use Illuminate\Support\Facades\Storage;
class MedicalAttachmentService
{
    public function __construct(
        protected MedicalAttachmentRepositoryInterface $repository
    ) {
    }
    public function store(array $data, int $recordId)
    {
        $filePath = $data['ray_image']->store('medical_attachments', 'public');

        $attachment = $this->repository->create([
            'patient_record_id' => $recordId,
            'ray_name' => $data['ray_name'],
            'ray_laboratory' => $data['ray_laboratory'],
            'ray_date' => $data['ray_date'],
            'ray_image' => $filePath // بدون مساحة
        ]);

        return $attachment->fresh(); // إعادة تحميل النموذج من قاعدة البيانات


    }

    public function update(int $id, array $data)
    {
        if (isset($data['ray_image'])) {
            $attachment = $this->repository->find($id);
            Storage::delete('public/' . $attachment->ray_image);
            $data['ray_image'] = $data['ray_image']->store('medical_attachments', 'public');
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        $attachment = $this->repository->find($id);
        Storage::delete('public/' . $attachment->ray_image);
        return $this->repository->delete($id);
    }

    public function getByPatientRecord(int $recordId)
    {
        return $this->repository->getByPatientRecord($recordId);
    }
}
