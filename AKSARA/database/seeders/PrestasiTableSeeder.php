<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrestasiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('prestasi')->insert([
            [
                'mahasiswa_id' => 1,
                'nama_prestasi' => 'Juara 1 Hackathon',
                'kategori' => 'akademik',
                'penyelenggara' => 'Hackathon ID',
                'tingkat' => 'nasional',
                'tahun' => 2024,
                'file_bukti' => 'prestasi1.pdf',
                'status_verifikasi' => 'disetujui',
                'catatan_verifikasi' => 'Sesuai format',
                'created_at' => now()
            ]
        ]);
    }
    
}
