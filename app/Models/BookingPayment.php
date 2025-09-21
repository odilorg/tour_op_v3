<?php

namespace App\Models;

use App\Enums\PaymentDirection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'direction',
        'method',
        'amount_minor',
        'currency_code',
        'paid_at',
        'reference',
        'notes',
    ];

    protected $casts = [
        'direction' => PaymentDirection::class,
        'paid_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }
}
