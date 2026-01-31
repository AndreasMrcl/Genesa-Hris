<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gps_attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('compani_id');
            $table->date('attendance_date');

            // Check-In Data
            $table->dateTime('check_in_time')->nullable();
            $table->decimal('check_in_latitude', 10, 8)->nullable();
            $table->decimal('check_in_longitude', 11, 8)->nullable();
            $table->text('check_in_address')->nullable();
            $table->decimal('check_in_distance', 8, 2)->nullable()->comment('Distance in meters');
            $table->string('check_in_photo')->nullable();

            // Check-Out Data
            $table->dateTime('check_out_time')->nullable();
            $table->decimal('check_out_latitude', 10, 8)->nullable();
            $table->decimal('check_out_longitude', 11, 8)->nullable();
            $table->text('check_out_address')->nullable();
            $table->decimal('check_out_distance', 8, 2)->nullable()->comment('Distance in meters');
            $table->string('check_out_photo')->nullable();

            // Status & Notes
            $table->enum('status', ['present', 'late', 'early_leave', 'incomplete'])->default('present');
            $table->text('notes')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('compani_id')->references('id')->on('companis')->onDelete('cascade');

            // Indexes
            $table->index(['employee_id', 'attendance_date']);
            $table->index(['compani_id', 'attendance_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('gps_attendance_logs');
    }
};
