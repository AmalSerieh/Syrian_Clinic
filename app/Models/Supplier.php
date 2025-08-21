<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [ 'secretary_id','sup_name', 'sup_phone','sup_photo'];

    public function supplierMaterials()
    {
        return $this->hasMany(SupplierMaterial::class);
    }
}
