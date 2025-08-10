<?php

namespace App\Models;

use App\Traits\Medication\HasMedication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Medication extends Model
{
    use HasMedication;
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
        'med_total_quantity',
        'med_taken_quantity'
    ];

    public function patientRecord()
    {
        return $this->belongsTo(Patient_record::class);
    }
    public function alarm()
    {
        return $this->hasMany(MedicationAlarm::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function getIsActiveAttribute()
    {
        if ($this->med_type !== 'current') {
            return true; // الأدوية المزمنة نشطة دائمًا
        }

        if (!$this->med_start_date || !$this->med_end_date) {
            return false;
        }

        return Carbon::now()->lte(Carbon::parse($this->med_end_date));
        // return $this->med_start_date && $this->med_end_date && $this->med_frequency_value > 0;
    }
    protected $appends = ['is_active', 'total_quantity'];

    public function getTotalQuantityAttribute()
    {
        $start = Carbon::parse($this->med_start_date);
        $end = ($this->med_type === 'current' && $this->med_end_date)
            ? Carbon::parse($this->med_end_date)
            : now();

        $days = $start->diffInDays($end) + 1;
        return ceil($days * $this->med_frequency_value);
    }

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


    /*  public function calculateProgressDetailed1()
     {
         if ($this->med_type !== 'current' || !$this->med_end_date || !$this->med_start_date) {
             return [
                 'progress' => null,
                 'taken_till_now' => 0,
             ];
         }

         $start = \Carbon\Carbon::parse($this->med_start_date);
         $end = \Carbon\Carbon::parse($this->med_end_date);
         $now = \Carbon\Carbon::now();

         if ($now->lt($start)) {
             return [
                 'progress' => 0,
                 'taken_till_now' => 0,
             ];
         }

         if ($now->gt($end)) {
             return [
                 'progress' => 100,
                 'taken_till_now' => $this->med_total_quantity, // المفروض أخذ كل الجرعات
             ];
         }

         // عدد الأيام الإجمالية
         $totalDays = $start->diffInDays($end) + 1;

         // عدد الأيام التي مضت
         $passedDays = $start->diffInDays($now) + 1;

         // عدد الجرعات حتى اليوم
         $takenTillNow = $passedDays * floatval($this->med_frequency_value);

         // النسبة:
         $progress = min(100, ($takenTillNow / $this->med_total_quantity) * 100);

         return [
             'progress' => round($progress, 2),
             'taken_till_now' => round($takenTillNow), // أو ممكن round فقط إذا كانت حبات
         ];
     } */

}
