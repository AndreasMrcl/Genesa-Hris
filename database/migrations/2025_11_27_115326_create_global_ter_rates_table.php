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
        Schema::create('global_ter_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compani_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('ter_category', ['A', 'B', 'C']); 
            
            // Range Penghasilan Bruto (Min - Max)
            $table->decimal('gross_income_min', 15, 2);
            $table->decimal('gross_income_max', 15, 2)->nullable(); // Nullable untuk range "Di atas X rupiah"
            
            // Tarif Pajak dalam Persen (Contoh: 0.5 untuk 0.5%)
            // Kita gunakan presisi tinggi (5 digit total, 2 di belakang koma)
            $table->decimal('rate_percentage', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_ter_rates');
    }
};