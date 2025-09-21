<?php

use App\Enums\SupplierRateServiceType;
use App\Enums\SupplierRateUnit;
use App\Enums\VehicleType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->enum('service_type', array_column(SupplierRateServiceType::cases(), 'value'));
            $table->enum('unit', array_column(SupplierRateUnit::cases(), 'value'));
            $table->enum('vehicle_type', array_column(VehicleType::cases(), 'value'))->nullable();
            $table->bigInteger('amount_minor');
            $table->string('currency_code');
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('currency_code')->references('code')->on('currencies');
            $table->unique(['supplier_id', 'service_type', 'unit', 'vehicle_type'], 'supplier_rates_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_rates');
    }
};
