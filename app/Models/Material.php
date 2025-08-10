<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'material_name',
        'material_quantity',
        'material_location',
        'material_expiration_date',
        'material_price',
        'material_threshold'
    ];

    public function supplierMaterials()
    {
        return $this->hasMany(SupplierMaterial::class);
    }

    public function doctorMaterials()
    {
        return $this->hasMany(DoctorMaterial::class);
    }
}
