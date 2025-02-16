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
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Link to the users table with cascade delete
            $table->foreignId('currency_id')->default(1);
            $table->decimal('basic_salary', 10, 2)->default(0); // Basic salary
            $table->decimal('bonus', 10, 2)->nullable(); // Bonus (if any)
            $table->decimal('deductions', 10, 2)->nullable(); // Deductions (if any)
            $table->decimal('net_salary', 10, 2)->default(0); // Final calculated salary
            $table->date('payment_date')->nullable(); // Salary payment date
            $table->enum('status', ['pending', 'paid'])->default('pending'); // Salary payment status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};
