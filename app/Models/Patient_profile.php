<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient_profile extends Model
{
    protected $fillable = [
        'patient_record_id',
        'gender',
        'date_birth',
        'height',
        'weight',
        'blood_type',
        'smoker',
        'alcohol',
        'drug',
        'matital_status'
    ];
    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
    public function getAcceptedBloodTypes(): array
    {
        $map = [
            'O-' => ['O-'],
            'O+' => ['O-', 'O+'],
            'A-' => ['O-', 'A-'],
            'A+' => ['O-', 'O+', 'A-', 'A+'],
            'B-' => ['O-', 'B-'],
            'B+' => ['O-', 'O+', 'B-', 'B+'],
            'AB-' => ['O-', 'A-', 'B-', 'AB-'],
            'AB+' => ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'],
            'Gwada-' => ['Gwada-'],
        ];

        return $map[$this->blood_type] ?? [];
    }

    public function getRejectedBloodTypes(): array
    {
        $all = ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+', 'Gwada-'];
        return array_diff($all, $this->getAcceptedBloodTypes());
    }
    // ✅ الحقول التي تحتاج تشفير
    protected $encryptable = [
       // 'gender',
       // 'date_birth',
        'height',
        'weight',
       // 'blood_type',
       // 'smoker',
        //'alcohol',
        //'drug',
        //'matital_status'
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
