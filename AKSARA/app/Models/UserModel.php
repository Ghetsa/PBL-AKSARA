<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Storage;

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
        'no_telepon',
        'alamat',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Accessor untuk mendapatkan URL foto profil.
     * Menampilkan avatar default jika foto tidak ada atau tidak ditemukan.
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto && Storage::disk('public')->exists($this->foto)) {
            return asset('storage/' . $this->foto);
        }

        // Avatar default berdasarkan role
        switch ($this->role) {
            case 'mahasiswa':
                return asset('mantis/dist/assets/images/user/1.jpg'); // Sesuaikan path jika berbeda
            case 'admin':
                return asset('mantis/dist/assets/images/user/2.jpg'); // Sesuaikan path jika berbeda
            case 'dosen':
                return asset('mantis/dist/assets/images/user/3.jpg'); // Sesuaikan path jika berbeda
            default:
                return asset('mantis/dist/assets/images/user/avatar-2.jpg'); // Fallback umum
        }
    }

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

    public function keahlian()
    {
        return $this->belongsToMany(BidangModel::class, 'keahlian_user', 'user_id', 'bidang_id')
            ->withPivot('keahlian_user_id', 'sertifikasi', 'status_verifikasi', 'catatan_verifikasi')
            ->withTimestamps()
            ->as('detailKeahlian');
    }

    public function minat()
    {
        return $this->belongsToMany(BidangModel::class, 'minat_user', 'user_id', 'bidang_id')
            ->withPivot('minat_user_id', 'level')
            ->withTimestamps()
            ->as('detailMinat');
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
