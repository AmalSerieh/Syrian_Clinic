<?php
namespace App\Services\Api\PateintRecord;

use App\Repositories\Api\PateintRecord\MedicalFileRepositoryInterface;
class MedicalFileService
{
    public function __construct(protected MedicalFileRepositoryInterface $repo)
    {
    }
    public function store(array $data, int $recordId)
    {
        $filePath = $data['test_image_pdf']->store('medical_files', 'public');

        return $this->repo->create([
            'patient_record_id' => $recordId,
            'test_name' => $data['test_name'],
            'test_laboratory' => $data['test_laboratory'],
            'test_date' => $data['test_date'],
            'test_image_pdf' => $filePath,
        ]);
    }
    public function getAllForPatient(int $recordId)
    {
        return $this->repo->getByPatientRecord($recordId);
    }
    public function getGroupedFilesByTypeAndTestName(int $recordId): array
    {
        $files = $this->repo->getByPatientRecord($recordId);

        return [
            'images' => $files->filter(fn($file) => in_array($file->getExtensionType(), ['image']))
                ->groupBy('test_name')
                ->map->values()
            ,

            'documents' => $files->filter(fn($file) => in_array($file->getExtensionType(), ['document']))
                ->groupBy('test_name')
                ->map->values()
            ,
        ];

    }
    public function getFilesByType(int $recordId, string $type)
    {
        $files = $this->repo->getByPatientRecord($recordId);

        return $files->filter(function ($file) use ($type) {
            $extension = strtolower(pathinfo($file->test_image_pdf, PATHINFO_EXTENSION));

            if ($type === 'images') {
                return in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);
            } else {
                return in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'pptx']);
            }
        });
    }


}
