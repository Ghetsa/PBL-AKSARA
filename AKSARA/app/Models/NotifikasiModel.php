<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiModel extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'notifikasi_id';

    protected $fillable = [
        'user_id',
        'lomba_id',
        'prestasi_id',
        'keahlian_user_id',
        'judul',
        'isi',
        'status_baca',
    ];

    protected $casts = [
        'status_baca' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function lomba()
    {
        return $this->belongsTo(LombaModel::class, 'lomba_id');
    }

    public function prestasi()
    {
        return $this->belongsTo(PrestasiModel::class, 'prestasi_id');
    }

    public function keahlianUser()
    {
        return $this->belongsTo(KeahlianUserModel::class, 'keahlian_user_id');
    }
}
