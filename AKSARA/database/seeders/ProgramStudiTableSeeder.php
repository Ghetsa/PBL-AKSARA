<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProgramStudiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('program_studi')->insert([
            ['nama' => 'Informatika', 'created_at' => now()],
            ['nama' => 'Sistem Informasi', 'created_at' => now()]
        ]);
    }

}
