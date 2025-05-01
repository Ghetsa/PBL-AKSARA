<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LombaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    DB::table('lomba')->insert([
        [
            'nama_lomba' => 'Lomba Inovasi Digital',
            'pembukaan_pendaftaran' => now()->subDays(10),
            'kategori' => 'akademik',
            'penyelenggara' => 'Kemendikbud',
            'tingkat' => 'nasional',
            'bidang_keahlian' => 'AI dan Data',
            'link_pendaftaran' => 'https://lomba.example.com',
            'batas_pendaftaran' => now()->addDays(20),
            'status_verifikasi' => 'disetujui',
            'diinput_oleh' => 1,
            'created_at' => now()
        ]
    ]);
}

}
