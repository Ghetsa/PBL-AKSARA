<?php

namespace App\Observers;

use App\Models\NotifikasiLombaModel;
use App\Models\LombaModel;

class NotifikasiLombaObserver
{
    public function updated(LombaModel $model)
    {
        if ($model->getOriginal('status_verifikasi') === $model->status_verifikasi) {
            return;
        }
        
        $judul = '';
        $pesan = '';

        switch ($model->status_verifikasi) {
            case 'ditolak':
                $judul = "Pengajuan Lomba Ditolak";
                $pesan = "Pengajuan lomba '{$model->nama_lomba}' telah ditolak.";
                break;
            case 'disetujui':
                $judul = "Pengajuan Lomba Disetujui";
                $pesan = "Pengajuan lomba '{$model->nama_lomba}' telah disetujui.";
                break;
            default:
                return;
        }

        // PERBAIKAN: Gunakan 'diinput_oleh' dari tabel lomba sebagai 'user_id'
        if ($model->diinput_oleh) {
            NotifikasiLombaModel::create([
                'lomba_id'     => $model->lomba_id,
                'user_id'      => $model->diinput_oleh, // Menggunakan kolom yang benar
                'judul'        => $judul,
                'isi'          => $pesan,
                'status_baca'  => 'belum_dibaca',
            ]);
        }
    }
}