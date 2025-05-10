<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdiModel extends Model
{
    use HasFactory;

    protected $table = 'program_studi';
    protected $primaryKey = 'prodi_id';

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama',
    ];

    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaModel::class, 'prodi_id', 'prodi_id');
    }
}