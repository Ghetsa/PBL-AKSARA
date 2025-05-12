<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminModel extends Model
{
    use HasFactory;

    protected $table = 'admin';
    protected $primaryKey = 'admin_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nip',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}