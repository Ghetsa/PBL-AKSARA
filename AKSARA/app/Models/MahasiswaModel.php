<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaModel extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'mahasiswa_id';

    protected $fillable = [
        'user_id',
        'nim',
        'prodi_id',
        'periode_id',
        'bidang_minat',
        'keahlian',
        'sertifikasi',
        'pengalaman',
    ];

    public $timestamps = true;

    // Relasi opsional (jika ada model terkait)
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function prodi()
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id');
    }

    public function periode()
    {
        return $this->belongsTo(PeriodeModel::class, 'periode_id');
    }


    public function keahlian()
    {
        return $this->belongsTo(KeahlianModel::class, 'keahlian_id', 'keahlian_id');
    }
}
