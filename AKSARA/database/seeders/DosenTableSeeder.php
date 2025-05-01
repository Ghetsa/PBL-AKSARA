<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DosenTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('dosen')->insert([
            [
                'user_id' => 1,
                'nip' => '198012341990021001',
                'bidang_keahlian' => 'Data Science',
                'created_at' => now()
            ]
        ]);
    }
    
}
