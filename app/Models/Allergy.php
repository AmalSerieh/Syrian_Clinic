<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $fillable = [
        'patient_record_id',
        'aller_power',
        'aller_name',
        'aller_type',
        'aller_cause',//المسبب
        'aller_treatment',//العلاج
        'aller_pervention',//المنوعات
        'aller_reasons'//الأسباب
    ];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
     // ✅ الحقول التي تحتاج تشفير
    protected $encryptable = [
        //'aller_power',
        'aller_name',
       // 'aller_type',
        'aller_cause',//المسبب
        'aller_treatment',//العلاج
        'aller_pervention',//المنوعات
        'aller_reasons'//الأسباب
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
