<?php

namespace App\Models;

use App\Enums\SupplierType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'contact_name',
        'phone',
        'email',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'type' => SupplierType::class,
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function rates()
    {
        return $this->hasMany(SupplierRate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
