<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('day_index');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('planned_duration_minutes')->nullable();
            $table->timestamps();

            $table->unique(['tour_id', 'day_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_days');
    }
};
