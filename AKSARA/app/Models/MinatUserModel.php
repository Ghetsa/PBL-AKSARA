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
        'minat_id',
        'user_id',
    ];

    // Relasi ke model Minat
    public function minat()
    {
        return $this->belongsTo(MinatModel::class, 'minat_id', 'minat_id');
    }

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
