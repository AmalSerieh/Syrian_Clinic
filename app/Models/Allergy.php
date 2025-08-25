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
    public function visit()
    {
        return $this->belongsTo(Visit::class);
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
// Accessors لكل حقل مشفر لتسهيل JSON
    public function getDecryptedAllerNameAttribute() { return $this->getDecryptedAttribute('aller_name'); }
    public function getDecryptedAllerCauseAttribute() { return $this->getDecryptedAttribute('aller_cause'); }
    public function getDecryptedAllerTreatmentAttribute() { return $this->getDecryptedAttribute('aller_treatment'); }
    public function getDecryptedAllerPerventionAttribute() { return $this->getDecryptedAttribute('aller_pervention'); }
    public function getDecryptedAllerReasonsAttribute() { return $this->getDecryptedAttribute('aller_reasons'); }


}
