<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('store_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id');
            $table->foreignId('commission_type_id');
            $table->foreignId('currency_id');
            $table->integer('commission');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('store_commissions');
    }
};
