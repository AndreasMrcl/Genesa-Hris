<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GlobalBpjs;
use App\Models\GlobalPtkp;

class BpjsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GlobalBpjs::updateOrCreate(
            ['id' => 1], 
            [
                'kes_comp_percent' => 4.00,
                'kes_emp_percent' => 1.00,
                'kes_cap_amount' => 12000000,
                'jht_comp_percent' => 3.70,
                'jht_emp_percent' => 2.00,
                'jp_comp_percent' => 2.00,
                'jp_emp_percent' => 1.00,
                'jp_cap_amount' => 10547400, // Update sesuai angka resmi 2024 (Rp 10.042.300)
                'jkm_comp_percent' => 0.30, // Standar umum JKM adalah 0.30%
            ]
        );

        // 2. Isi Aturan PTKP & Kategori TER (PP 58 Tahun 2023)
        $ptkps = [
            // === KATEGORI A ===
            ['code' => 'TK/0', 'amount' => 54000000, 'ter_category' => 'A'],
            ['code' => 'TK/1', 'amount' => 58500000, 'ter_category' => 'A'],
            ['code' => 'K/0',  'amount' => 58500000, 'ter_category' => 'A'],
            
            // === KATEGORI B ===
            ['code' => 'TK/2', 'amount' => 63000000, 'ter_category' => 'B'],
            ['code' => 'TK/3', 'amount' => 67500000, 'ter_category' => 'B'],
            ['code' => 'K/1',  'amount' => 63000000, 'ter_category' => 'B'],
            ['code' => 'K/2',  'amount' => 67500000, 'ter_category' => 'B'],

            // === KATEGORI C ===
            ['code' => 'K/3',  'amount' => 72000000, 'ter_category' => 'C'],
        ];

        foreach ($ptkps as $ptkp) {
            GlobalPtkp::updateOrCreate(
                ['code' => $ptkp['code']], // Kunci pencarian
                $ptkp // Data update
            );
        }
    }
}
