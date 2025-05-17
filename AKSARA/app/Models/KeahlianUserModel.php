<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeahlianUserModel extends Model
{
    use HasFactory;

    protected $table = 'keahlian_user';
    protected $primaryKey = 'keahlian_user_id';
    public $timestamps = true;

    protected $fillable = [
        'keahlian_id',
        'user_id',
        'sertifikasi',
        'status_verifikasi',
        'catatan_verifikasi',
    ];

    // Relasi ke tabel keahlian
    public function keahlian()
    {
        return $this->belongsTo(KeahlianModel::class, 'keahlian_id', 'keahlian_id');
    }

    // Relasi ke tabel users (pastikan kolomnya benar)
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id'); 
    }
}
