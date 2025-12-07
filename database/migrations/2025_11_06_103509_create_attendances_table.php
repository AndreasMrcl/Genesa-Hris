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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('compani_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('total_present')->default(0);
            $table->integer('total_late')->default(0);
            $table->integer('total_sick')->default(0);
            $table->integer('total_permission')->default(0);
            $table->integer('total_permission_letter')->default(0);
            $table->integer('total_alpha')->default(0);
            $table->integer('total_leave')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['compani_id', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};