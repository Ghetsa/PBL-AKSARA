<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini jika Anda menggunakan Model Factories
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class UserModel extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = ['nama', 'email', 'password', 'role', 'status'];
    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
        // Tambahkan 'email_verified_at' => 'datetime', jika Anda menggunakan fitur verifikasi email
    ];
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

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}