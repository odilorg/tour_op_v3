<?php

use App\Enums\BookingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->enum('old_status', array_column(BookingStatus::cases(), 'value'))->nullable();
            $table->enum('new_status', array_column(BookingStatus::cases(), 'value'));
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('changed_at');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_status_logs');
    }
};
