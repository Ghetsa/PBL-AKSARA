<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiKeahlianModel extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_keahlian';
    // PERBAIKAN: Sesuaikan dengan nama kolom di database
    protected $primaryKey = 'notifikasi_keahlian_id';

    protected $fillable = [
        'user_id',
        'keahlian_user_id',
        'judul',
        'isi',
        'status_baca',
        'link',
    ];

    // Accessor untuk membuat atribut virtual 'id'
    public function getIdAttribute()
    {
        return $this->attributes['notifikasi_keahlian_id'];
    }

    // Accessor untuk membuat alias 'type'
    public function getTypeAttribute()
    {
        return 'keahlian';
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function keahlianUser()
    {
        return $this->belongsTo(KeahlianUserModel::class, 'keahlian_user_id', 'keahlian_user_id');
    }
}