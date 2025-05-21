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
        'foto',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /** JWT Implementation */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * One-to-One relations
     */
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

    /**
     * Many-to-Many relation to Bidang via keahlian_user (gabungan minat & keahlian)
     */
    public function bidang()
    {
        return $this->belongsToMany(
            BidangModel::class,
            'keahlian_user',
            'user_id',
            'bidang_id'
        )
        ->withPivot('keahlian_user_id', 'sertifikasi', 'status_verifikasi', 'catatan_verifikasi')
        ->withTimestamps();
    }

    /**
     * One-to-Many relation to KeahlianUserModel
     */
    public function keahlianUser()
    {
        return $this->hasMany(KeahlianUserModel::class, 'user_id', 'user_id');
    }

    /**
     * One-to-Many relation to MinatUserModel
     */
    public function minatUser()
    {
        return $this->hasMany(MinatUserModel::class, 'user_id', 'user_id');
    }

    /**
     * Other relations
     */
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
