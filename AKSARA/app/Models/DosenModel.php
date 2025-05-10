<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DosenModel extends Model
{
    protected $table = 'dosen';
    protected $primaryKey = 'dosen_id';
    // public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nip',
        'keahlian_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
        
    public function keahlian()
    {
        return $this->belongsTo(KeahlianModel::class, 'keahlian_id', 'keahlian_id');
    }
}
