<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MahasiswaBimbinganTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('mahasiswa_bimbingan')->insert([
            [
                'dosen_id' => 1,
                'mahasiswa_id' => 1,
                'aktif' => true,
                'created_at' => now()
            ]
        ]);
    }
    
}
