<?php

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'type',
        'plate',
        'seats',
        'notes',
    ];

    protected $casts = [
        'type' => VehicleType::class,
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function assignments()
    {
        return $this->hasMany(BookingAssignment::class);
    }
}
