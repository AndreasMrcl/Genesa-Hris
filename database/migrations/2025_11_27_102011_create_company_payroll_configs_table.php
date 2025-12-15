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
             // BPJS Kesehatan
            $table->decimal('kes_comp_percent', 5, 2)->default(4.0); //perusahaan 4%
            $table->decimal('kes_emp_percent', 5, 2)->default(1.0); //karyawan 1%
            $table->decimal('kes_cap_amount', 15, 2)->default(1200000); //batas maksimal upah
            // JHT (Jaminan Hari Tua)
            $table->decimal('jht_comp_percent', 5, 2)->default(3.7); //perusahaan 3.7%
            $table->decimal('jht_emp_percent', 5, 2)->default(2.0); //karyawan 2%
            // JP (Jaminan Pensiun)
            $table->decimal('jp_comp_percent', 5, 2)->default(2.0); //perusahaan 2%
            $table->decimal('jp_emp_percent', 5, 2)->default(1.0); //karyawan 1%
            $table->decimal('jp_cap_amount', 15, 2)->default(10547400); //batas maksimal upah
            // JKM (Kematian)
            $table->decimal('jkm_comp_percent', 5, 2)->default(0.30); //perusahaan 0.30%
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