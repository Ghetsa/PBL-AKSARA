<?php

namespace App\Traits;

use App\Models\User;
use App\Models\NotifikasiLombaModel;
use App\Models\NotifikasiPrestasiModel;
use App\Models\NotifikasiKeahlianModel;
use Illuminate\Database\Eloquent\Model;

trait NotifikasiUntukAdmin
{
    /**
     * Mengirim notifikasi ke semua pengguna dengan peran 'admin'.
     *
     * @param \Illuminate\Database\Eloquent\Model $model Model yang baru dibuat (Lomba, Prestasi, Keahlian)
     * @param string $tipeNotifikasi Tipe notifikasi ('Lomba', 'Prestasi', 'Keahlian')
     * @param string $namaPengaju Nama mahasiswa yang mengajukan
     */
    protected static function kirimNotifikasiKeAdmin(Model $model, string $tipeNotifikasi, string $namaPengaju): void
    {
        // Ambil semua user admin
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            return; // Tidak ada admin, tidak perlu kirim notifikasi
        }

        $judul = "Pengajuan {$tipeNotifikasi} Baru";
        $modelNotifikasi = null;
        $data = [];

        // Siapkan data berdasarkan tipe notifikasi
        switch ($tipeNotifikasi) {
            case 'Lomba':
                $pesan = "Mahasiswa '{$namaPengaju}' telah mengajukan lomba baru: '{$model->nama_lomba}'.";
                $modelNotifikasi = NotifikasiLombaModel::class;
                $data = ['lomba_id' => $model->lomba_id, 'judul' => $judul, 'isi' => $pesan];
                break;
            case 'Prestasi':
                $pesan = "Mahasiswa '{$namaPengaju}' telah mengajukan prestasi baru: '{$model->nama_prestasi}'.";
                $modelNotifikasi = NotifikasiPrestasiModel::class;
                $data = ['prestasi_id' => $model->prestasi_id, 'judul' => $judul, 'isi' => $pesan];
                break;
            case 'Keahlian':
                $pesan = "Mahasiswa '{$namaPengaju}' telah mengajukan keahlian baru: '{$model->nama_sertifikat}'.";
                $modelNotifikasi = NotifikasiKeahlianModel::class;
                $data = ['keahlian_user_id' => $model->keahlian_user_id, 'judul' => $judul, 'isi' => $pesan];
                break;
        }

        // Kirim notifikasi ke setiap admin
        foreach ($admins as $admin) {
            $modelNotifikasi::create(array_merge($data, ['user_id' => $admin->user_id]));
        }
    }
}