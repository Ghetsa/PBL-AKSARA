<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LombaModel extends Model
{
    use HasFactory;

    protected $table = 'lomba';
    protected $primaryKey = 'lomba_id';
    public $timestamps = true;

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

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'diinput_oleh', 'user_id');
    }
}
