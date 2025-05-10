<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekomendasiLombaModel extends Model
{
    use HasFactory;

    protected $table = 'rekomendasi_lomba';
    protected $primaryKey = 'rekomendasi_id';

    public $timestamps = false;

    protected $fillable = [
        'mahasiswa_id',
        'lomba_id',
        'alasan',
        'skor_kecocokan',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }

    public function lomba()
    {
        return $this->belongsTo(LombaModel::class, 'lomba_id', 'lomba_id');
    }
}