<?php

namespace App\Observers;

use App\Models\KeahlianUserModel;
use App\Models\NotifikasiKeahlianModel;
use App\Traits\NotifikasiUntukAdmin; // <-- Tambahkan ini

class NotifikasiKeahlianObserver
{
    use NotifikasiUntukAdmin; // <-- Tambahkan ini

    /**
     * Handle the KeahlianUserModel "created" event.
     */
    public function created(KeahlianUserModel $model)
    {
        $namaPengaju = $model->user->nama ?? 'Mahasiswa';
        self::kirimNotifikasiKeAdmin($model, 'Keahlian', $namaPengaju);
    }

    /**
     * Handle the KeahlianUserModel "updated" event.
     */
    public function updated(KeahlianUserModel $model)
    {
        // ... (kode method updated Anda yang sudah ada, biarkan saja)
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