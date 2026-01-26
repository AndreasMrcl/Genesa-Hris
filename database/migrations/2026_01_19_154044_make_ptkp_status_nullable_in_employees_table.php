<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('ptkp_status')
                  ->nullable()
                  ->default(null) 
                  ->change();
        });

        DB::table('employees')->update(['ptkp_status' => null]);
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            DB::table('employees')
                ->whereNull('ptkp_status')
                ->update(['ptkp_status' => 'TK/0']);

            $table->string('ptkp_status')
                  ->nullable(false)
                  ->default('TK/0')
                  ->change();
        });
    }
};
