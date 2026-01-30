<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('source', ['fingerspot', 'gps', 'mixed', 'manual'])
                ->default('manual')
                ->after('employee_id');
            $table->integer('total_late')
                ->default(0)
                ->after('period_end');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->dropColumn('total_late');
        });
    }
};