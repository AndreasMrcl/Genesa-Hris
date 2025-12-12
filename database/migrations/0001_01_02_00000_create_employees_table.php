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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('compani_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('nik', 20)->unique();
            $table->string('fingerprint_id')->nullable();
            $table->string('npwp', 20)->nullable();
            $table->string('ktp', 20)->unique();
            $table->string('bpjs_kesehatan_no')->nullable();
            $table->string('bpjs_ketenagakerjaan_no')->nullable();
            $table->string('ptkp_status')->default('TK/0');
            $table->string('phone');
            $table->string('address');
            $table->decimal('base_salary', 15, 2);
            $table->integer('working_days')->default(27);
            $table->enum('payroll_method', ['transfer', 'cash'])->default('transfer');
            $table->string('bank_name')->nullable();
            $table->string('bank_account_no')->nullable();
            $table->boolean('participates_bpjs_kes')->default(true);
            $table->boolean('participates_bpjs_tk')->default(true);
            $table->boolean('participates_bpjs_jp')->default(true);
            $table->date('join_date');
            $table->enum('status', ['PKWT', 'PKWTT', 'DAILY_WORKER'])->default('PKWT');
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
