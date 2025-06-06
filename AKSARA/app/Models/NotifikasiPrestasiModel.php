<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiPrestasiModel extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_prestasi';
    protected $primaryKey = 'notifikasi_prestasi_id';

    protected $fillable = [
        'user_id',
        'prestasi_id',
        'judul',
        'isi',
        'status_baca',
        'link',
    ];

    // Accessor untuk membuat atribut virtual 'id'
    public function getIdAttribute()
    {
        return $this->attributes['notifikasi_prestasi_id'];
    }

    // Accessor untuk membuat alias 'type'
    public function getTypeAttribute()
    {
        return 'prestasi';
    }
    
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function prestasi()
    {
        return $this->belongsTo(PrestasiModel::class, 'prestasi_id', 'prestasi_id');
    }
}