<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient_record extends Model
{
    protected $fillable = [
        'patient_id',
        'profile_submitted',
        'diseases_submitted',
        'operations_submitted',
        'tests_submitted',
        'allergies_submitted',
        'family_history_submitted',
        'medications_submitted',
        'medicalfiles_submitted'
    ];
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    //السجل العام
    public function patient_profile()
    {
        return $this->hasOne(Patient_profile::class);

    }
    //الأمراض
    public function diseases()
    {
        return $this->hasMany(Disease::class);

    }
    //الأدوية
    public function medications()
    {
        return $this->hasMany(Medication::class);

    }
    //العمليات
    public function operations()
    {
        return $this->hasMany(Operation::class);

    }
    //الفحوصات الطبية
    public function tests()
    {
        return $this->hasMany(Test::class);
    }

    //الحساسيات
    public function allergies()
    {
        return $this->hasMany(Allergy::class);

    }
    //تاريخ العائلة المرضي
    public function familyHistories()
    {
        return $this->hasMany(FamilyHistory::class);

    }
    //المرفقات من صور و ملفات
    public function medicalFiles()
    {
        return $this->hasMany(MedicalFile::class);

    }

    // ✅ تهيئة تلقائية بعد إنشاء السجل لأول مرة
    /*   protected static function booted()
       {
           static::created(function ($record) {
               // إنشاء الأقسام التي تكون One-to-One
                $record->patient_profile()->create([]);
               // الأقسام التي هي One-to-Many (نبدأها بسجل واحد افتراضي أو لا شيء)
               $record->allergies()->create([]);
               $record->operations()->create([]);
               $record->diseases()->create([]);
               $record->medications()->create([]);
               $record->tests()->create([]);
               $record->familyHistories()->create([]);
               $record->medicalFiles()->create([]);


           });
       }*/
}
