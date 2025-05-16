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
        'user_id',
        'minat',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    protected $casts = [
        'minat' => 'string',
    ];

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
        // Tambahkan opsi lain jika ada
    ];
}
