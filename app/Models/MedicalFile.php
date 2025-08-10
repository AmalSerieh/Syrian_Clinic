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

    public function visit()
    {
        return $this->belongsTo(Visit::class);
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
    // ✅ الحقول التي تحتاج تشفير
    protected $encryptable = [
        'test_name',
        'test_laboratory',
        'test_image_pdf'
    ];
    // 🔐 التعامل مع التشفير قبل التخزين
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->encryptable) && !is_null($value)) {
            $value = encrypt($value);
        }
        return parent::setAttribute($key, $value);
    }

    // 🔐 فك التشفير عند الجلب
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if (in_array($key, $this->encryptable) && !is_null($value)) {
            try {
                return decrypt($value);
            } catch (\Exception $e) {
                return $value; // في حال كان النص غير مشفر
            }
        }
        return $value;
    }




}
