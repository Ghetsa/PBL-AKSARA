<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LombaModel extends Model
{
    use HasFactory;

    protected $table = 'lomba';
    protected $primaryKey = 'lomba_id';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'nama_lomba',
        'pembukaan_pendaftaran',
        'kategori',
        'penyelenggara',
        'tingkat',
        'bidang_keahlian',
        'link_pendaftaran',
        'batas_pendaftaran',
        'status_verifikasi',
        'diinput_oleh',
    ];

    protected $casts = [
        'pembukaan_pendaftaran' => 'date',
        'batas_pendaftaran' => 'date',
        'kategori' => 'string',
        'tingkat' => 'string',
        'status_verifikasi' => 'string',
        'tahun' => 'integer',
    ];

    public function inputBy()
    {
        return $this->belongsTo(UserModel::class, 'diinput_oleh', 'user_id');
    }

    public function pendaftar()
    {
        return $this->hasMany(PendaftaranLombaModel::class, 'lomba_id', 'lomba_id');
    }

    public function rekomendasi()
    {
        return $this->hasMany(RekomendasiLombaModel::class, 'lomba_id', 'lomba_id');
    }
}