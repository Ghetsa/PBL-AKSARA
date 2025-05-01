<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            ProgramStudiTableSeeder::class,
            PeriodeTableSeeder::class,
            MahasiswaTableSeeder::class,
            DosenTableSeeder::class,
            LombaTableSeeder::class,
            PrestasiTableSeeder::class,
            PendaftaranLombaTableSeeder::class,
            RekomendasiLombaTableSeeder::class,
            NotifikasiTableSeeder::class,
            FeedbackDosenTableSeeder::class,
            MahasiswaBimbinganTableSeeder::class,
            KompetensiMahasiswaTableSeeder::class,
        ]);
    }    
}
