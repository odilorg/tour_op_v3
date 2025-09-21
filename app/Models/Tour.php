<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_active',
        'default_currency_code',
    ];

    protected $casts = [
        'is_active' => 'bool',
    ];

    public function days(): HasMany
    {
        return $this->hasMany(TourDay::class)->orderBy('day_index');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'default_currency_code', 'code');
    }
}
