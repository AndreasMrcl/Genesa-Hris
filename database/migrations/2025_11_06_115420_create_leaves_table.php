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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compani_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('type', [
                'izin',
                'sakit',
                'cuti',
                'meninggalkan_pekerjaan',
                'tukar_shift',
                'other',
            ]);
            // Tanggal mulai & selesai cuti
            $table->date('start_date');
            $table->date('end_date');
            // Alasan atau keterangan tambahan
            $table->text('note')->nullable();
            // Status approval (workflow sederhana)
            $table->enum('status', [
                'pending',   // menunggu approval
                'approved',  // disetujui
                'rejected',  // ditolak
                'cancelled'  // dibatalkan oleh karyawan
            ])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};
