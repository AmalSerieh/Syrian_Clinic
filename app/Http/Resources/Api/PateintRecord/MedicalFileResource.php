<?php

namespace App\Http\Resources\Api\PateintRecord;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class MedicalFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fileExtension = strtolower(pathinfo($this->test_image_pdf, PATHINFO_EXTENSION));

        return [
            'id' => $this->id,
            'test_name' => $this->test_name,
            'test_laboratory' => $this->test_laboratory,
            'test_date' => $this->test_date,
            'file_url' => asset('storage/' . $this->test_image_pdf),
            'file_type' => $this->getFileType($fileExtension),
            'file_icon' => $this->getFileIcon($fileExtension),
        ];
    }
    protected function getFileType($extension)
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'webp'];
        $documentTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'pptx'];

        if (in_array($extension, $imageTypes)) {
            return 'image';
        } elseif (in_array($extension, $documentTypes)) {
            return 'document';
        }

        return 'file';
    }
    protected function getFileIcon($extension)
    {
        $icons = [
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'webp' => 'fa-file-image',
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'pptx' => 'fa-file-powerpoint',
        ];

        return $icons[$extension] ?? 'fa-file';
    }
}
