<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SliderKriteriaModel extends Model
{
    use HasFactory;

    protected $table = 'slider_kriteria';
    protected $primaryKey = 'slider_id';
    public $timestamps = false;

    /**
     * [PERBAIKAN] Fillable disesuaikan dengan nama kolom baru di database.
     */
    protected $fillable = [
        'user_id', // Menggunakan user_id
        'minat',
        'keahlian',
        'tingkat',
        'hadiah',
        'penutupan', // Menggunakan penutupan
        'biaya',     // Menggunakan biaya
    ];

    /**
     * [PERBAIKAN] Relasi sekarang langsung ke UserModel.
     */
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}