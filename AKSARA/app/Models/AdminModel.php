<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminModel extends Model
{
    protected $table = 'admin';
    protected $primaryKey = 'admin_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nip',
        'create_at',
        'update_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
