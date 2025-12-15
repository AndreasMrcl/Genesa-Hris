<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('global_bpjs', function (Blueprint $table) {
            $table->id();
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
        Schema::dropIfExists('global_bpjs');
    }
};