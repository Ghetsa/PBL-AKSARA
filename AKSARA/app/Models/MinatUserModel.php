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
