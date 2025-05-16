<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeahlianModel extends Model
{
    protected $table = 'keahlian';
    protected $primaryKey = 'keahlian_id';
    public $timestamps = true;

    // Daftar pilihan keahlian yang tersedia (statis)
    const PILIHAN_KEAHLIAN = [
        'Pemrograman',
        'Desain Grafis',
        'Manajemen Proyek',
        'Analisis Data',
        'Jaringan Komputer',
        'Kecerdasan Buatan',
        'Keamanan Siber',
        'Machine Learning',
        'UI/UX',
        'Cloud Computing',
        'Fotografi',
        'Desain Grafis',
        'Musik',
        'Sepak Bola / Futsal',
        'E-Sports',
        'Bulu Tangkis',
        'Kategori Kewirausahaan',
        'Startup Teknologi',
        'Business Plan',
        'Digital Marketing',
    ];

    // Relasi many-to-many dengan User (melalui pivot keahlian_user)
    public function users()
    {
        return $this->belongsToMany(UserModel::class, 'keahlian_user', 'keahlian_id', 'user_id')
            ->withPivot('sertifikasi')
            ->withTimestamps();
    }

}
