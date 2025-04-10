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
        Schema::create('currency_exchanges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->foreignId('from_currency_id')->constrained('currencies')->onDelete('cascade');
            $table->foreignId('to_currency_id')->constrained('currencies')->onDelete('cascade');
            $table->decimal('amount', 20, 4); // How much was exchanged from the "from" currency
            $table->decimal('received_amount', 20, 4); // How much was received in the "to" currency
            $table->decimal('rate', 10, 4); // Actual exchange rate used at the time
            $table->decimal('commission', 10, 4)->nullable(); // Optional: commission charged
            $table->date('date'); // Date of exchange
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_exchanges');
    }
};
