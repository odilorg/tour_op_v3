<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'old_status',
        'new_status',
        'user_id',
        'changed_at',
        'note',
    ];

    protected $casts = [
        'old_status' => BookingStatus::class,
        'new_status' => BookingStatus::class,
        'changed_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
