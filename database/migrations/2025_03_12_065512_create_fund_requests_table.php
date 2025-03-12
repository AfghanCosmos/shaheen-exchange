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
        Schema::create('fund_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('offline_transfer_id');
            $table->foreignId('store_id')->constrained('stores')->onDelete('cascade');
            $table->decimal('amount_requested', 16, 2);
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('restrict');
            $table->enum('status', ['pending_admin_approval', 'approved', 'rejected'])->default('pending_admin_approval');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_requests');
    }
};
