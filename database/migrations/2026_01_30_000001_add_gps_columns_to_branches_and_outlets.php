<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('gps_radius')->default(5000)->after('longitude')->comment('Radius in meters');
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('gps_radius')->default(5000)->after('longitude')->comment('Radius in meters');
        });
    }

    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'gps_radius']);
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'gps_radius']);
        });
    }
};