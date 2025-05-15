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
        'nama_prestasi',
        'kategori',
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
}
