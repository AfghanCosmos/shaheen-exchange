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
        Schema::create('add_to_wallets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->morphs('owner'); // Morph for dynamic relationship (user or store)
            $table->foreignId('currency_id');
            $table->string('giver_name');
            $table->decimal('amount', 16, 2);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'failed'])->default('pending');
            $table->foreignId('add_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->decimal('commission_amount', 16, 2)->default(0.0);
            $table->boolean('verified_by_store')->default(false);
            $table->text('details')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_to_wallets');
    }
};
