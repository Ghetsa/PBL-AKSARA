<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class KompetensiMahasiswaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('kompetensi_mahasiswa')->insert([
            [
                'mahasiswa_id' => 1,
                'jenis' => 'keahlian',
                'deskripsi' => 'Machine Learning',
                'created_at' => now()
            ],
            [
                'mahasiswa_id' => 1,
                'jenis' => 'minat',
                'deskripsi' => 'Data Science',
                'created_at' => now()
            ]
        ]);
    }
    
}
