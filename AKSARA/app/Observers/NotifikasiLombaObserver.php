<?php

namespace App\Observers;

use App\Models\LombaModel;
use App\Models\NotifikasiLombaModel;
use App\Traits\NotifikasiUntukAdmin; // <-- Tambahkan ini

class NotifikasiLombaObserver
{
    use NotifikasiUntukAdmin; // <-- Tambahkan ini

    /**
     * Handle the LombaModel "created" event.
     * Mengirim notifikasi ke admin saat lomba baru dibuat.
     */
    public function created(LombaModel $model)
    {
        // Ambil nama mahasiswa yang menginput
        $namaPengaju = $model->penginput->nama ?? 'Mahasiswa';
        self::kirimNotifikasiKeAdmin($model, 'Lomba', $namaPengaju);
    }

    /**
     * Handle the LombaModel "updated" event.
     * Mengirim notifikasi ke mahasiswa saat status verifikasi berubah.
     */
    public function updated(LombaModel $model)
    {
        // ... (kode method updated Anda yang sudah ada, biarkan saja)
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

        if ($model->diinput_oleh) {
            NotifikasiLombaModel::create([
                'lomba_id'     => $model->lomba_id,
                'user_id'      => $model->diinput_oleh,
                'judul'        => $judul,
                'isi'          => $pesan,
                'status_baca'  => 'belum_dibaca',
            ]);
        }
    }
}