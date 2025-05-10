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
    // DB::table('users')->insert([
        // [
        //     'nama' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'admin',
        //     'status' => 'aktif',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ],
        // [
        //     'nama' => 'Mahasiswa User',
        //     'email' => 'mhs@example.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'mahasiswa',
        //     'status' => 'aktif',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ]

        // [
        //     'nama' => 'Ghetsa',
        //     'email' => 'ghetsa@example.com',
        //     'password' => bcrypt('123456'),
        //     'role' => 'mahasiswa',
        //     'status' => 'aktif',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ],
        // [
        //     'nama' => 'Wawan',
        //     'email' => 'wawan@example.com',
        //     'password' => bcrypt('123456'),
        //     'role' => 'dosen',
        //     'status' => 'aktif',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ],
        // [
        //     'nama' => 'Sony',
        //     'email' => 'soni@example.com',
        //     'password' => bcrypt('123456'),
        //     'role' => 'admin',
        //     'status' => 'aktif',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ],
        // [
        //     'nama' => 'Reika',
        //     'email' => 'reika@example.com',
        //     'password' => bcrypt('123456'),
        //     'role' => 'Mahasiswa',
        //     'status' => 'aktif',
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ],

        DB::table('users')->insert([
            ['username' => 'admin1', 'email' => 'admin1@example.com', 'password' => bcrypt('123456'), 'role' => 'admin'],
            ['username' => 'admin2', 'email' => 'admin2@example.com', 'password' => bcrypt('123456'), 'role' => 'admin'],
            ['username' => 'dosen1', 'email' => 'dosen1@example.com', 'password' => bcrypt('123456'), 'role' => 'dosen'],
            ['username' => 'dosen2', 'email' => 'dosen2@example.com', 'password' => bcrypt('123456'), 'role' => 'dosen'],
            ['username' => 'dosen3', 'email' => 'dosen3@example.com', 'password' => bcrypt('123456'), 'role' => 'dosen'],
            ['username' => 'mhs1', 'email' => 'mhs1@example.com', 'password' => bcrypt('123456'), 'role' => 'mahasiswa'],
            ['username' => 'mhs2', 'email' => 'mhs2@example.com', 'password' => bcrypt('123456'), 'role' => 'mahasiswa'],
            ['username' => 'mhs3', 'email' => 'mhs3@example.com', 'password' => bcrypt('123456'), 'role' => 'mahasiswa'],
            ['username' => 'mhs4', 'email' => 'mhs4@example.com', 'password' => bcrypt('123456'), 'role' => 'mahasiswa'],
            ['username' => 'mhs5', 'email' => 'mhs5@example.com', 'password' => bcrypt('123456'), 'role' => 'mahasiswa'],
            ['username' => 'mhs6', 'email' => 'mhs6@example.com', 'password' => bcrypt('123456'), 'role' => 'mahasiswa'],
            ['username' => 'mhs7', 'email' => 'mhs7@example.com', 'password' => bcrypt('123456'), 'role' => 'mahasiswa'],
        ]);
    // ]);
}

}
