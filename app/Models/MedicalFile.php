<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalFile extends Model
{
    protected $fillable = [
        'patient_record_id',
        'test_name',
        'test_laboratory',
        'test_date',
        'test_image_pdf'
    ];

    protected $casts = [
        'file_image_paths' => 'array',
    ];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
    public function getExtensionType(): string
    {
        $extension = strtolower(pathinfo($this->test_image_pdf, PATHINFO_EXTENSION));
        return match ($extension) {
            'jpg', 'jpeg', 'png', 'webp' => 'image',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'pptx' => 'document',
            default => 'other',
        };
    }

}
