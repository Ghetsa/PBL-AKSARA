<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MahasiswaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('mahasiswa')->insert([
            [
                'user_id' => 2,
                'nim' => '123456789',
                'prodi_id' => 1,
                'periode_id' => 1,
                'bidang_minat' => 'AI',
                'keahlian' => 'Python',
                'sertifikasi' => 'Cisco',
                'pengalaman' => 'Magang di PT X',
                'created_at' => now()
            ]
        ]);
    }
    
}
