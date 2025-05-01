<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    DB::table('users')->insert([
        [
            'nama' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'nama' => 'Mahasiswa User',
            'email' => 'mhs@example.com',
            'password' => bcrypt('password'),
            'role' => 'mahasiswa',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ]);
}

}
