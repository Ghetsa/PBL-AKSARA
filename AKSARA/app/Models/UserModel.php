<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function admin()
    {
        return $this->hasOne(AdminModel::class, 'user_id', 'user_id');
    }
    public function dosen()
    {
        return $this->hasOne(DosenModel::class, 'user_id', 'user_id');
    }
    public function mahasiswa()
    {
        return $this->hasOne(MahasiswaModel::class, 'user_id', 'user_id');
    }
    // Dalam UserModel
    public function keahlian()
    {
        return $this->belongsToMany(KeahlianModel::class, 'keahlian_user', 'user_id', 'keahlian_id')
            ->withPivot('sertifikasi')
            ->withTimestamps();
    }

    public function minat()
    {
        return $this->belongsToMany(MinatModel::class, 'minat_user', 'user_id', 'minat_id')
            ->withTimestamps();
    }



    public function pengalaman()
    {
        return $this->hasMany(PengalamanModel::class, 'user_id', 'user_id');
    }
    public function notifikasi()
    {
        return $this->hasMany(NotifikasiModel::class, 'user_id', 'user_id');
    }
    public function lombaDiinput()
    {
        return $this->hasMany(LombaModel::class, 'diinput_oleh', 'user_id');
    }
}
