<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FamilyHistory extends Model
{
    protected $fillable = ['patient_record_id', 'family_name', 'kinship'];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    // ✅ الحقول التي تحتاج تشفير
    protected $encryptable = [
        'family_name',
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
