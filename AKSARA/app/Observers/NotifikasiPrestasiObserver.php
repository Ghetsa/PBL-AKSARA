<?php

namespace App\Observers;

use App\Models\PrestasiModel;
use App\Models\NotifikasiPrestasiModel;
use App\Models\DosenModel; // Pastikan model Dosen sudah ada dan di-import
use App\Traits\NotifikasiUntukAdmin;

class NotifikasiPrestasiObserver
{
    use NotifikasiUntukAdmin;

    /**
     * Handle the PrestasiModel "created" event.
     */
    public function created(PrestasiModel $model)
    {
        // ===================================================================
        // BAGIAN 1: KIRIM NOTIFIKASI KE SEMUA ADMIN (Tetap berjalan)
        // ===================================================================
        $namaPengaju = $model->mahasiswa->user->nama ?? 'Mahasiswa';
        self::kirimNotifikasiKeAdmin($model, 'Prestasi', $namaPengaju);

        // ===================================================================
        // BAGIAN 2: KIRIM NOTIFIKASI KE DOSEN PEMBIMBING (Logika Baru)
        // ===================================================================
        if ($model->dosen_id) { // Cek apakah ada dosen pembimbing yang dipilih
            
            // Cari data user dari dosen yang bersangkutan berdasarkan dosen_id
            $dosen = DosenModel::find($model->dosen_id);

            // Jika dosen dan user_id-nya ditemukan
            if ($dosen && $dosen->user_id) {
                NotifikasiPrestasiModel::create([
                    'prestasi_id'  => $model->prestasi_id,
                    'user_id'      => $dosen->user_id, // <-- Target notifikasi adalah user_id milik Dosen
                    'judul'        => "Penunjukan Pembimbing Prestasi",
                    'isi'          => "Anda telah ditunjuk sebagai pembimbing untuk prestasi '{$model->nama_prestasi}' yang diajukan oleh {$namaPengaju}.",
                    'status_baca'  => 'belum_dibaca',
                ]);
            }
        }
    }

    /**
     * Handle the PrestasiModel "updated" event.
     */
    public function updated(PrestasiModel $model)
    {
        // Method updated ini untuk notifikasi ke mahasiswa, tidak perlu diubah.
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
        
        $mahasiswa = $model->mahasiswa;
        if ($mahasiswa && $mahasiswa->user_id) {
            NotifikasiPrestasiModel::create([
                'prestasi_id'  => $model->prestasi_id,
                'user_id'      => $mahasiswa->user_id,
                'judul'        => $judul,
                'isi'          => $pesan,
                'status_baca'  => 'belum_dibaca',
            ]);
        }
    }
}