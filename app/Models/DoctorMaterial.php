<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'material_id',
        'supplier_id',
        'visit_id',
        'dm_quantity',
        'dm_quality',
        'dm_used_at',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
