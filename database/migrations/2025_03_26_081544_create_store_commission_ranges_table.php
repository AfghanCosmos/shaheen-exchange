<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('store_commission_ranges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id');
            $table->foreignId('currency_id');
            $table->string('from');
            $table->string('to');
            $table->integer('commission');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_commission_ranges');
    }
};
