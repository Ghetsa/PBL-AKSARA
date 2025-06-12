<?php

namespace App\Observers;

use App\Models\LombaModel;
use App\Models\NotifikasiLombaModel;
use App\Traits\NotifikasiUntukAdmin;
// use Illuminate\Support\Facades\Auth; // Tidak perlu jika relasi sudah benar

class NotifikasiLombaObserver
{
    use NotifikasiUntukAdmin;

    /**
     * Handle the LombaModel "created" event.
     * Mengirim notifikasi ke admin saat lomba baru dibuat oleh mahasiswa.
     */
    public function created(LombaModel $model)
    {
        // ===================================================================
        // PERBAIKAN UTAMA: Cek role user yang membuat data
        // ===================================================================
        // Kita hanya kirim notifikasi jika ada relasi 'penginput' dan rolenya BUKAN 'admin'.
        if ($model->penginput && $model->penginput->role !== 'admin') {
            $namaPengaju = $model->penginput->nama ?? 'Mahasiswa';
            self::kirimNotifikasiKeAdmin($model, 'Lomba', $namaPengaju);
        }
        // Jika yang membuat adalah admin, maka tidak ada notifikasi yang dikirim.
    }

    /**
     * Handle the LombaModel "updated" event.
     * Mengirim notifikasi ke mahasiswa saat status verifikasi berubah.
     */
    public function updated(LombaModel $model)
    {
        // Bagian ini sudah benar, tidak perlu diubah.
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