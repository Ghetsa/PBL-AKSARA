<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KeahlianModel extends Model
{
    protected $table = 'keahlian';
    protected $primaryKey = 'keahlian_id';
    public $timestamps = true;

    protected $fillable = [
        // 'user_id',
        'keahlian_nama',
        // 'sertifikasi', // Path ke file sertifikat
        // 'status_verifikasi',
        // 'catatan_verifikasi',
    ];

    // Daftar pilihan keahlian yang tersedia (statis)
    public static function getPilihanKeahlian()
    {
        return self::orderBy('keahlian_nama')->pluck('keahlian_nama')->toArray();
    }


    // Relasi many-to-many dengan User (melalui pivot keahlian_user)
    // public function users()
    // {
    //     return $this->belongsToMany(UserModel::class, 'keahlian_user', 'keahlian_id', 'user_id')
    //         ->withPivot('sertifikasi')
    //         ->withTimestamps();
    // }

    // Definisikan nilai ENUM untuk status verifikasi
    // public const STATUS_VERIFIKASI = ['pending', 'disetujui', 'ditolak'];

    // public function user()
    // {
    //     return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    // }

    // Relasi ke User (Many-to-Many)
    public function users()
    {
        return $this->belongsToMany(UserModel::class, 'keahlian_user', 'keahlian_id', 'user_id')
            ->withPivot('keahlian_user_id', 'sertifikasi', 'status_verifikasi', 'catatan_verifikasi')
            ->withTimestamps();
    }

    // Accessor untuk mendapatkan URL lengkap file sertifikasi
    // public function getSertifikasiUrlAttribute()
    // {
    //     if ($this->sertifikasi && Storage::disk('public')->exists($this->sertifikasi)) {
    //         return Storage::url($this->sertifikasi);
    //     }
    //     return null;
    // }
}
