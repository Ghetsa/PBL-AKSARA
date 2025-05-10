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

    public function admin() { return $this->hasOne(AdminModel::class, 'user_id', 'user_id'); }
    public function dosen() { return $this->hasOne(DosenModel::class, 'user_id', 'user_id'); }
    public function mahasiswa() { return $this->hasOne(MahasiswaModel::class, 'user_id', 'user_id'); }
    public function keahlian() { return $this->hasMany(KeahlianModel::class, 'user_id', 'user_id'); }
    public function minat() { return $this->hasMany(MinatModel::class, 'user_id', 'user_id'); }
    public function pengalaman() { return $this->hasMany(PengalamanModel::class, 'user_id', 'user_id'); }
    public function notifikasi() { return $this->hasMany(NotifikasiModel::class, 'user_id', 'user_id'); }
    public function lombaDiinput() { return $this->hasMany(LombaModel::class, 'diinput_oleh', 'user_id'); }
}