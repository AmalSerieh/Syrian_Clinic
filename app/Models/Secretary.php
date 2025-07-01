<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secretary extends Model
{
    protected $fillable = [
        'user_id',
        'photo',
        'date_of_appointment'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
