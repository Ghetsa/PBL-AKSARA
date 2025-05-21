<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinatUserModel extends Model
{
    use HasFactory;

    protected $table = 'minat_user';
    protected $primaryKey = 'minat_user_id';
    public $timestamps = true;

    protected $fillable = [
        'bidang_id',
        'user_id',
        'level',
    ];

    // Definisikan konstanta untuk opsi ENUM level
    public const LEVEL_MINAT = [
        'kurang' => 'Kurang Minat',
        'minat' => 'Minat',
        'sangat minat' => 'Sangat Minat',
    ];
    // Atau jika hanya mau valuenya:
    // public const OPSI_LEVEL = ['kurang', 'minat', 'sangat minat'];

    // Relasi ke bidang (Many-to-One)
    public function bidang()
    {
        return $this->belongsTo(BidangModel::class, 'bidang_id', 'bidang_id');
    }

    // Relasi ke user (Many-to-One)
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
