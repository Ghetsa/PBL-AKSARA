<?php

namespace App\Observers;

use App\Models\NotifikasiKeahlianModel;
use App\Models\KeahlianUserModel;

class NotifikasiKeahlianObserver
{
    public function updated(KeahlianUserModel $model)
    {
        if ($model->getOriginal('status_verifikasi') === $model->status_verifikasi) {
            return;
        }

        // Tentukan judul dan pesan berdasarkan status
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

        // Kirim notifikasi
        NotifikasiKeahlianModel::create([
            'keahlian_user_id' => $model->keahlian_user_id,
            'user_id'      => $model->user_id,
            'judul'        => $judul,
            'isi'          => $pesan,
            'status_baca'  => 'belum_dibaca',
            // 'link'         => route('keahlian.show', ['id' => $model->id]),
        ]);
    }
}
