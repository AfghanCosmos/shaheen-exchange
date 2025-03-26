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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_currency_id')->constrained('currencies')->onDelete('cascade'); // From currency
            $table->foreignId('to_currency_id')->constrained('currencies')->onDelete('cascade'); // To currency
            $table->decimal('buy_rate', 10, 4); // Exchange rate (e.g., 1 USD = 80 AFN)
            $table->decimal('sell_rate', 10, 4); // Exchange rate (e.g., 1 USD = 80 AFN)
            $table->date('date'); // Date of the exchange rate
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
