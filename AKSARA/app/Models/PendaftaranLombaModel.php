<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranLombaModel extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_lomba';
    protected $primaryKey = 'pendaftaran_id';

    public $timestamps = false;

    protected $fillable = [
        'mahasiswa_id',
        'lomba_id',
        'status_pendaftaran',
        'hasil',
        'dosen_pembimbing_id',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaModel::class, 'mahasiswa_id', 'mahasiswa_id');
    }

    public function lomba()
    {
        return $this->belongsTo(LombaModel::class, 'lomba_id', 'lomba_id');
    }

    public function dosenPembimbing()
    {
        return $this->belongsTo(DosenModel::class, 'dosen_pembimbing_id', 'dosen_id');
    }

    protected $casts = [
        'status_pendaftaran' => 'string',
    ];
}