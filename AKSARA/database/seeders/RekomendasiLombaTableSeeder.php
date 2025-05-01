<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RekomendasiLombaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('rekomendasi_lomba')->insert([
            [
                'mahasiswa_id' => 1,
                'lomba_id' => 1,
                'alasan' => 'Cocok dengan minat AI',
                'skor_kecocokan' => 87.5,
                'created_at' => now()
            ]
        ]);
    }
    
}
