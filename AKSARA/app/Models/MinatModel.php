<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinatModel extends Model
{
    use HasFactory;

    protected $table = 'minat';
    protected $primaryKey = 'minat_id';

    protected $fillable = [
        'minat_nama',
    ];

    public function user()
    {
        return $this->belongsToMany(UserModel::class, 'minat_user', 'minat_id', 'user_id')
            ->withTimestamps();
    }

    // public const PILIHAN_MINAT = [
    //     'Pemrograman',
    //     'Desain Grafis',
    //     'Manajemen Proyek',
    //     'Analisis Data',
    //     'Jaringan Komputer',
    //     'Kecerdasan Buatan',
    //     'Keamanan Siber',
    //     'Machine Learning',
    //     'UI/UX',
    //     'Cloud Computing',
    //     'Fotografi',
    //     'Desain Grafis',
    //     'Musik',
    //     'Sepak Bola / Futsal',
    //     'E-Sports',
    //     'Bulu Tangkis',
    //     'Kategori Kewirausahaan',
    //     'Startup Teknologi',
    //     'Business Plan',
    //     'Digital Marketing',
    // ];
}
