<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MedicalFilesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'file_image_paths' => collect($this->file_image_paths)->map(function ($path) {
                return [
                    'path' => $path,
                    'url' => $this->resolveUrl($path),
                    'preview_type' => $this->getPreviewType($path),
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }

    protected function resolveUrl($path)
    {
        // إذا كان الرابط خارجي (يبدأ بـ http أو https)
        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }

        // إذا كان مسار داخلي
        return asset('storage/' . $path);
    }

    protected function getPreviewType($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
        $pdfExtensions = ['pdf'];
        $docExtensions = ['doc', 'docx', 'xls', 'xlsx','pptx'];

        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $pdfExtensions)) {
            return 'pdf';
        } elseif (in_array($extension, $docExtensions)) {
            return 'document';
        }

        return 'file';
    }
}
