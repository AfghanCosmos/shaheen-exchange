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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 16, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('payment_gateway')->nullable();
            $table->string('reference_id')->nullable();
            $table->enum('source', ['manual', 'card', 'bank', 'crypto', 'referral']);
            $table->foreignId('referral_id')->nullable();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
