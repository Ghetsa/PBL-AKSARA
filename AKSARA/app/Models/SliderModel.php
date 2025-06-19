<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderKriteria extends Model
{
    protected $table = 'slider_kriteria';
    protected $primaryKey = 'slider_id';

    protected $fillable = [
        'mahasiswa_id',
        'minat',
        'keahlian',
        'tingkat',
        'hadiah',
        'waktu_pendaftaran',
        'biaya_pendaftaran',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }
}
