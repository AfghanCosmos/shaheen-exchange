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
        Schema::create('hawlas', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->datetime('date');
            $table->foreignId('hawla_type_id')->nullable();
            $table->string('sender_name');
            $table->string('sender_phone');
            $table->string('receiver_name');
            $table->string('receiver_father')->nullable();
            $table->foreignId('sender_store_id');
            $table->float('given_amount', 16, 2);
            $table->foreignId('given_amount_currency_id');
            $table->foreignId('receiving_amount_currency_id');
            $table->float('receiving_amount', 16, 2);
            $table->float('exchange_rate')->nullable();
            $table->float('commission')->nullable();
            $table->enum('commission_taken_by', ['sender_store', 'receiver_store'])->default('sender_store');
            $table->string('receiver_phone_number', 20)->nullable();
            $table->text('receiver_address')->nullable();
            $table->foreignId('receiver_store_id');
            $table->text('note')->nullable();
            $table->foreignId('created_by');
            $table->string('receiver_verification_document')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hawlas');
    }
};
