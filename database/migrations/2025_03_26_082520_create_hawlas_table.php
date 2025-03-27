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

            // Sender information
            $table->string('sender_name');
            $table->string('sender_phone');

            // Receiver information
            $table->string('receiver_name');
            $table->string('receiver_email')->nullable();
            $table->string('receiver_phone_number', 20)->nullable();
            $table->text('receiver_address')->nullable();

            // Transfer information
            $table->decimal('amount', 16, 2);
            $table->foreignId('sent_currency_id')->constrained('currencies')->onDelete('restrict');
            $table->foreignId('given_currency_id')->constrained('currencies')->onDelete('restrict');

            // Store-to-store relation (optional)
            $table->foreignId('from_store_id')->nullable()->constrained('stores')->onDelete('set null');
            $table->foreignId('to_store_id')->nullable()->constrained('stores')->onDelete('set null');

            // Status tracking
            $table->enum('status', [
                'pending',
                'waiting_for_receiver',
                'store_requested',
                'admin_approved',
                'completed',
                'failed'
            ])->default('pending');

            // Extra info
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            // Time tracking
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
