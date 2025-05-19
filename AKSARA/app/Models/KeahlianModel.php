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
        'keahlian_nama',
    ];

    // Daftar pilihan keahlian yang tersedia (statis)
    public static function getPilihanKeahlian()
    {
        return self::orderBy('keahlian_nama')->pluck('keahlian_nama')->toArray();
    }

    // Relasi ke User (Many-to-Many)
    public function users()
    {
        return $this->belongsToMany(UserModel::class, 'keahlian_user', 'keahlian_id', 'user_id')
            ->withPivot('keahlian_user_id', 'sertifikasi', 'status_verifikasi', 'catatan_verifikasi')
            ->withTimestamps();
    }
}
