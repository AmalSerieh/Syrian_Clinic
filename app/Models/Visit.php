<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'appointment_id',
        'doctor_id',
        'patient_id',
        'v_started_at',
        'v_ended_at',
        'v_status',
        'v_price',
        'v_paid',
        'v_notes'
    ];

    //السجل العام
    public function patient_profile()
    {
        return $this->hasMany(Patient_profile::class);

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
    public function medicalAttachment()
    {
        return $this->hasMany(MedicalAttachment::class);
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
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

}
