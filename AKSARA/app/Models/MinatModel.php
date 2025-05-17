<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinatModel extends Model
{
    use HasFactory;

    protected $table = 'minat';
    protected $primaryKey = 'minat_id';

    protected $fillable = [
        'minat_nama',
    ];

    public function user()
    {
        return $this->belongsToMany(UserModel::class, 'minat_user', 'minat_id', 'user_id')
            ->withTimestamps();
    }

    public static function getPilihanMinat()
    {
        return self::orderBy('minat_nama')->pluck('minat_nama')->toArray();
    }

}
