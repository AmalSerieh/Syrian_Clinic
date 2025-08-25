<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = [
        'secretary_id',
        'material_name',
        'material_quantity',//الكمية الموجود من هذه المادة
        'material_location',//موقع وجود المادة في العيادة
        'material_expiration_date',//تاريخ انتهاء الصلاحية
        'material_price',//سعر كل مادة
        'material_threshold',
        'material_image'
    ];

    public function supplierMaterials()
    {
        return $this->hasMany(SupplierMaterial::class);
    }
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_materials')
            ->withPivot('sup_material_quantity', 'sup_material_price', 'sup_material_delivered_at', 'sup_material_is_damaged')
            ->withTimestamps();
    }

    public function doctorMaterials()
    {
        return $this->hasMany(DoctorMaterial::class);
    }
}
