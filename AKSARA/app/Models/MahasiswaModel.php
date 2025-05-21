<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaModel extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'mahasiswa_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nim',
        'prodi_id',
        'periode_id',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id', 'prodi_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodeModel::class, 'periode_id', 'periode_id');
    }

    public function bimbingan()
    {
        return $this->hasMany(MahasiswaBimbinganModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }

    public function pendaftaranLomba()
    {
        return $this->hasMany(PendaftaranLombaModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }

    public function prestasi()
    {
        return $this->hasMany(PrestasiModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }

    public function rekomendasiLomba()
    {
        return $this->hasMany(RekomendasiLombaModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }

    public function bidang()
    {
        return $this->belongsTo(BidangModel::class, 'keahlian_id', 'keahlian_id');
    }

    
}