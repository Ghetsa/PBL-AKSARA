<?php

namespace App\Observers;

use App\Models\KeahlianUserModel;
use App\Models\NotifikasiKeahlianModel;
use App\Traits\NotifikasiUntukAdmin;

class NotifikasiKeahlianObserver
{
    use NotifikasiUntukAdmin;

    /**
     * Handle the KeahlianUserModel "created" event.
     */
    public function created(KeahlianUserModel $model)
    {
        // ===================================================================
        // PERBAIKAN UTAMA: Cek role user yang membuat data
        // ===================================================================
        // Kita hanya kirim notifikasi jika ada relasi 'user' dan rolenya BUKAN 'admin'.
        if ($model->user && $model->user->role !== 'admin') {
            $namaPengaju = $model->user->nama ?? 'Mahasiswa';
            self::kirimNotifikasiKeAdmin($model, 'Keahlian', $namaPengaju);
        }
    }

    /**
     * Handle the KeahlianUserModel "updated" event.
     */
    public function updated(KeahlianUserModel $model)
    {
        // Bagian ini sudah benar, tidak perlu diubah.
        if ($model->getOriginal('status_verifikasi') === $model->status_verifikasi) {
            return;
        }

        $judul = '';
        $pesan = '';

        switch ($model->status_verifikasi) {
            case 'ditolak':
                $judul = "Keahlian Pengajuan Ditolak";
                $pesan = "Pengajuan keahlian Anda telah ditolak.";
                break;
            case 'disetujui':
                $judul = "Keahlian Pengajuan Disetujui";
                $pesan = "Pengajuan keahlian Anda telah disetujui.";
                break;
            default:
                return;
        }

        NotifikasiKeahlianModel::create([
            'keahlian_user_id' => $model->keahlian_user_id,
            'user_id'      => $model->user_id,
            'judul'        => $judul,
            'isi'          => $pesan,
            'status_baca'  => 'belum_dibaca',
        ]);
    }
}