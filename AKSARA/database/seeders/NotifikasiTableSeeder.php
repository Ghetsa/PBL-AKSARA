<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotifikasiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('notifikasi')->insert([
            [
                'user_id' => 2,
                'judul' => 'Pendaftaran Lomba Baru!',
                'isi' => 'Segera daftarkan diri pada lomba Inovasi Digital.',
                'status_baca' => 'belum',
                'created_at' => now()
            ]
        ]);
    }
    
}
