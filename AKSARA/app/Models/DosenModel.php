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
        'bidang_keahlian',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
