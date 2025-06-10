<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestasiModel extends Model
{
    use HasFactory;

    protected $table = 'prestasi';
    protected $primaryKey = 'prestasi_id';
    public $timestamps = false;


    protected $fillable = [
        'mahasiswa_id',
        'dosen_id',
        'nama_prestasi',
        'kategori',
        'bidang_id',
        'penyelenggara',
        'tingkat',
        'tahun',
        'file_bukti',
        'status_verifikasi',
        'catatan_verifikasi',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }
    public function bidang()
    {
        return $this->belongsTo(BidangModel::class, 'bidang_id', 'bidang_id');
    }
    public function dosen()
    {
        return $this->belongsTo(DosenModel::class, 'dosen_id');
    }

    public function feedbackDosen()
    {
        return $this->hasMany(FeedbackDosenModel::class, 'prestasi_id', 'prestasi_id');
    }

    // Dalam PrestasiModel.php
    public function dosenPembimbing()
    {
        return $this->belongsTo(DosenModel::class, 'dosen_id', 'dosen_id'); // Sesuaikan foreign dan owner key jika berbeda
    }

    protected $casts = [
        'kategori' => 'string',
        'tingkat' => 'string',
        'status_verifikasi' => 'string',
        'tahun' => 'integer',
    ];

    public function getStatusVerifikasiBadgeAttribute(): string
    {
        $status = strtolower($this->status_verifikasi);
        $badgeClass = 'bg-light-secondary text-secondary ';
        $label = ucfirst($status ?: 'Belum Diajukan');

        switch ($status) {
            case 'disetujui':
                $badgeClass = 'bg-light-success text-success';
                $label = 'Disetujui';
                break;
            case 'pending':
                $badgeClass = 'bg-light-warning text-warning';
                $label = 'Menunggu';
                break;
            case 'ditolak':
                $badgeClass = 'bg-light-danger text-danger';
                $label = 'Ditolak';
                break;
        }
        return '<span class="badge ' . $badgeClass . ' px-2 py-1 fs-6">' . $label . '</span>';
    }
}
