<?php

use App\Enums\BookingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference_code')->unique();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('party_size');
            $table->enum('status', array_column(BookingStatus::cases(), 'value'))->default(BookingStatus::DRAFT->value);
            $table->decimal('markup_percent', 5, 2)->default(0);
            $table->bigInteger('list_total_minor')->default(0);
            $table->bigInteger('cost_total_minor')->default(0);
            $table->bigInteger('profit_minor')->default(0);
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->string('currency_code');
            $table->boolean('manual_status_override')->default(false);
            $table->timestamps();

            $table->foreign('currency_code')->references('code')->on('currencies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
