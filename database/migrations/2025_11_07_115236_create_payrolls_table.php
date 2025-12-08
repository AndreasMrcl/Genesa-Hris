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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compani_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('pay_period_start'); // Awal periode gaji
            $table->date('pay_period_end');   // Akhir periode gaji
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('total_allowances', 15, 2)->default(0);  // tunjangan
            $table->decimal('total_deductions', 15, 2)->default(0);  // potongan
            $table->decimal('net_salary', 15, 2)->default(0);         // gaji bersih
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};