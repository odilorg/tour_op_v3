<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'date',
        'day_index',
        'title',
        'description',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(BookingAssignment::class);
    }
}
