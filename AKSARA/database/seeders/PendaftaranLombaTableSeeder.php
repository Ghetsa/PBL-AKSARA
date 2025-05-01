<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendaftaranLombaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    DB::table('pendaftaran_lomba')->insert([
        [
            'mahasiswa_id' => 1,
            'lomba_id' => 1,
            'status_pendaftaran' => 'diterima',
            'hasil' => 'Juara 2',
            'dosen_pembimbing_id' => 1,
            'created_at' => now()
        ]
    ]);
}

}
