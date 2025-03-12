<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offline_transfers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('sender_wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->string('receiver_name');
            $table->string('receiver_email')->nullable();
            $table->string('receiver_phone_number', 20)->nullable();
            $table->text('receiver_address')->nullable();
            $table->decimal('amount', 16, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->enum('status', ['pending', 'waiting_for_receiver', 'store_requested', 'admin_approved', 'completed', 'failed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_transfers');
    }
};
