<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('outlet_id')->nullable()->after('branch_id')->constrained('outlets')->onDelete('set null');
        });

        $branches = DB::table('branches')->get();

        foreach ($branches as $branch) {
            // Buat 'Default Outlet' untuk setiap branch yang ada
            $outletId = DB::table('outlets')->insertGetId([
                'branch_id' => $branch->id,
                'name' => 'Main Outlet - ' . $branch->name, // Nama sementara
                'phone' => $branch->phone,
                'address' => $branch->address,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update semua karyawan di branch ini agar masuk ke outlet baru tersebut
            DB::table('employees')
                ->where('branch_id', $branch->id)
                ->update(['outlet_id' => $outletId]);
        }
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
            $table->dropColumn('outlet_id');
        });

        Schema::dropIfExists('outlets');
    }
};