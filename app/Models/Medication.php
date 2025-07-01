<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Medication extends Model
{
    protected $fillable = [
        'patient_record_id',
        'med_type',
        'med_name',
        'med_start_date',
        'med_end_date',
        'med_frequency',
        'med_frequency_value',
        'med_dosage_form',
        'med_dose',
        'med_timing',
        'med_quantity_per_dose',
        'med_prescribed_by_doctor',
        'med_total_quantity'
    ];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
    protected $appends = ['is_active', 'total_quantity'];

    public function getIsActiveAttribute()
    {
        if ($this->med_type === 'chronic') {
            return true;
        }
        if ($this->med_end_date && now()->lte($this->med_end_date)) {
            return true;
        }
        return false;
    }
    /*  public function getTotalQuantityAttribute()
    {
        $start = Carbon::parse($this->med_start_date);
        $end = ($this->med_type === 'current' && $this->med_end_date)
            ? Carbon::parse($this->med_end_date)
            : now();

        $days = $start->diffInDays($end) + 1;
        return ceil($days * $this->med_frequency_value);
    }
 */
    // ✅ الحقول التي تحتاج تشفير
    protected $encryptable = [
        'med_name',
        'med_prescribed_by_doctor',
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
