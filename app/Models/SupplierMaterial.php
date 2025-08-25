<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierMaterial extends Model
{
    protected $fillable = [
        'supplier_id',
        'material_id',
        'sup_material_quantity',//كمية المادة من هذا المورد
        'sup_material_price',//سعر المادة من هذا المورد
        'sup_material_delivered_at',//تاريخ التسليم
        'sup_material_is_damaged'//إتلاف المادة 
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
