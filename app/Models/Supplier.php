<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['sup_name', 'sup_phone'];

    public function supplierMaterials()
    {
        return $this->hasMany(SupplierMaterial::class);
    }
}
