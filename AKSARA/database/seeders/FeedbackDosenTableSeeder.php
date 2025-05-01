<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeedbackDosenTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('feedback_dosen')->insert([
            [
                'dosen_id' => 1,
                'prestasi_id' => 1,
                'komentar' => 'Bagus, lanjutkan!',
                'status_validasi' => 'disetujui',
                'created_at' => now()
            ]
        ]);
    }
    
}
