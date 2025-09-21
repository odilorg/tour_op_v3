<?php

namespace App\Models;

use App\Enums\SupplierRateServiceType;
use App\Enums\SupplierRateUnit;
use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'service_type',
        'unit',
        'vehicle_type',
        'amount_minor',
        'currency_code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'service_type' => SupplierRateServiceType::class,
        'unit' => SupplierRateUnit::class,
        'vehicle_type' => VehicleType::class,
        'is_active' => 'bool',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
