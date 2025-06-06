<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifikasiLombaModel extends Model
{
    use HasFactory;

    protected $table = 'notifikasi_lomba';
    protected $primaryKey = 'notifikasi_lomba_id';

    protected $fillable = [
        'user_id',
        'lomba_id',
        'judul',
        'isi',
        'status_baca',
        'link',
    ];
    
    // Accessor untuk membuat atribut virtual 'id'
    public function getIdAttribute()
    {
        return $this->attributes['notifikasi_lomba_id'];
    }

    // Accessor untuk membuat alias 'type'
    public function getTypeAttribute()
    {
        return 'lomba';
    }

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function lomba()
    {
        return $this->belongsTo(LombaModel::class, 'lomba_id', 'lomba_id');
    }
}