<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeModel extends Model
{
    use HasFactory;

    protected $table = 'periode';
    protected $primaryKey = 'periode_id';

    public $timestamps = false;

    protected $fillable = [
        'semester',
        'tahun_akademik',
    ];

    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaModel::class, 'periode_id', 'periode_id');
    }
}