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
        Schema::create('company_payroll_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compani_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('bpjs_jkk_rate', 5, 2)->default(0.24);
            $table->boolean('bpjs_kes_active')->default(true);
            $table->boolean('bpjs_tk_active')->default(true);
            $table->enum('tax_method', ['GROSS', 'NET', 'GROSS_UP'])->default('GROSS');
            $table->decimal('ump_amount', 15, 2)->default(0);
            $table->decimal('infaq_percent', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_payroll_configs');
    }
};