<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    protected $fillable = [
        'doctor_id',
        'specialist_ar',//الاختصاص
        'specialist_en',//الاختصاص
        'cer_place',//مكان الحصول على الشهادة
        'cer_name',//اسم الشهادة
        'cer_images',//صورة الشهادة
        'cer_date',//تاريخ الحصول على الشهادة
        'exp_place',//مكان الخبرة
        'exp_years',//سنوات الخبرة
        'biography',//سيرة ذاتية
        'gender',//الجنس
        'date_birth',//ناريخ الميلاد
        //بحيث يتم حساب العمر من تاريخ الميلاد و يتم االتحديث التقائي أي كل ما بيكبير سننة بيتحدث ل حالو
    ];
    protected $casts = [
        'date_birth' => 'date',
    ];
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    // يتم احتساب العمر تلقائيًا
    public function getAgeAttribute(): int|null
    {
        return $this->date_birth ? $this->date_birth->age : null;
    }

}
