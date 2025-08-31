<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
class ClinicBill extends Model
{
     use HasFactory;

    protected $fillable = ['description', 'amount', 'billed_at'];
    protected $casts = [ 'billed_at' => 'date' ];

    public function scopeBetween(Builder $q, ?string $from, ?string $to): Builder
    {
        if ($from && $to) return $q->whereBetween('billed_at', [$from, $to]);
        if ($from) return $q->where('billed_at', '>=', $from);
        if ($to) return $q->where('billed_at', '<=', $to);
        return $q;
    }
}
