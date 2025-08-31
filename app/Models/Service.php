<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['serv_name'];

    public function nurses()
    {
        return $this->belongsToMany(Service::class, 'nurse_services', 'nurse_id', 'service_id')->withTimestamps();
        
    }
}

