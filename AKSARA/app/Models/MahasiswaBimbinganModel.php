<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MahasiswaBimbinganModel extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa_bimbingan';
    protected $primaryKey = 'bimbingan_id';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'dosen_id',
        'mahasiswa_id',
        'aktif',
    ];

    public function dosen()
    {
        return $this->belongsTo(DosenModel::class, 'dosen_id', 'dosen_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }

     protected $casts = [
         'aktif' => 'boolean',
     ];
}