<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeahlianModel extends Model
{
    use HasFactory;

    protected $table = 'keahlian';
    protected $primaryKey = 'keahlian_id';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

    protected $fillable = [
        'user_id',
        'keahlian_nama',
        'sertifikasi',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function dosen()
    {
        return $this->hasMany(DosenModel::class, 'keahlian_id', 'keahlian_id');
    }
}
