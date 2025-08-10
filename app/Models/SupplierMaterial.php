<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierMaterial extends Model
{
     protected $fillable = ['supplier_id', 'material_id', 'sup_material_quantity', 'sup_material_price', 'sup_material_delivered_at','sup_material_is_damaged'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
