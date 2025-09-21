<?php

use App\Enums\BookingAssignmentRole;
use App\Enums\BookingAssignmentStatus;
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
        Schema::create('booking_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_day_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('role', array_column(BookingAssignmentRole::cases(), 'value'));
            $table->enum('service_type', array_column(SupplierRateServiceType::cases(), 'value'))->nullable();
            $table->enum('unit', array_column(SupplierRateUnit::cases(), 'value'))->nullable();
            $table->enum('vehicle_type', array_column(VehicleType::cases(), 'value'))->nullable();
            $table->unsignedInteger('qty')->default(1);
            $table->bigInteger('rate_minor');
            $table->bigInteger('cost_minor')->default(0);
            $table->bigInteger('line_total_minor')->default(0);
            $table->bigInteger('cost_total_minor')->default(0);
            $table->string('currency_code');
            $table->enum('status', array_column(BookingAssignmentStatus::cases(), 'value'))->default(BookingAssignmentStatus::PENDING->value);
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('currency_code')->references('code')->on('currencies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_assignments');
    }
};
