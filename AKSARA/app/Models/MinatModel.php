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

    public function users()
    {
        return $this->belongsToMany(UserModel::class, 'minat_user', 'minat_id', 'user_id')
                    ->withTimestamps();
    }

    public const PILIHAN_MINAT = [
        'Web Development',
        'Mobile Development',
        'Data Science',
        'Machine Learning',
        'Cyber Security',
        'Cloud Computing',
        'DevOps',
        'UI/UX Design',
        'Database Administration',
        'Network Engineering'
    ];
}
