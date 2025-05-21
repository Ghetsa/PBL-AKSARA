<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengalamanModel extends Model
{
    use HasFactory;

    protected $table = 'pengalaman';
    protected $primaryKey = 'pengalaman_id';

    protected $fillable = [
        'user_id',
        'bidang_id',
        'pengalaman_nama',
        'pengalaman_kategori',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
    public function bidang()
    {
        return $this->hasOne(BidangModel::class, 'bidang_id', 'bidang_id');
    }
    protected $casts = [
        'pengalaman_kategori' => 'string',
    ];
}