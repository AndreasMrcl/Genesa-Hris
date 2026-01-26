<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            $table->decimal('overtime_pay', 15, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('overtimes', function (Blueprint $table) {
            DB::table('overtimes')->whereNull('overtime_pay')->update(['overtime_pay' => 0]);

            $table->decimal('overtime_pay', 15, 2)->nullable(false)->default(0)->change();
        });
    }
};
