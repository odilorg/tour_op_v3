<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('default_currency_code');
            $table->timestamps();

            $table->foreign('default_currency_code')->references('code')->on('currencies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
