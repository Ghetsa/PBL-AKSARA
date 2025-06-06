<?php

namespace App\Observers;

use App\Models\NotifikasiPrestasiModel;
use App\Models\PrestasiModel;

class NotifikasiPrestasiObserver
{
    public function updated(PrestasiModel $model)
    {
        if ($model->getOriginal('status_verifikasi') === $model->status_verifikasi) {
            return;
        }

        $judul = '';
        $pesan = '';

        switch ($model->status_verifikasi) {
            case 'ditolak':
                $judul = "Pengajuan Prestasi Ditolak";
                $pesan = "Pengajuan prestasi '{$model->nama_prestasi}' telah ditolak.";
                break;
            case 'disetujui':
                $judul = "Pengajuan Prestasi Disetujui";
                $pesan = "Pengajuan prestasi '{$model->nama_prestasi}' telah disetujui.";
                break;
            default:
                return;
        }
        
        // PERBAIKAN: Dapatkan user_id dari relasi mahasiswa
        $mahasiswa = $model->mahasiswa;
        if ($mahasiswa && $mahasiswa->user_id) {
            NotifikasiPrestasiModel::create([
                'prestasi_id'  => $model->prestasi_id,
                'user_id'      => $mahasiswa->user_id, // Mengambil dari relasi
                'judul'        => $judul,
                'isi'          => $pesan,
                'status_baca'  => 'belum_dibaca',
            ]);
        }
    }
}