<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackDosenModel extends Model
{
    use HasFactory;

    protected $table = 'feedback_dosen';
    protected $primaryKey = 'feedback_id';

    public $timestamps = false;

    protected $fillable = [
        'dosen_id',
        'prestasi_id',
        'komentar',
        'status_validasi',
    ];

    public function dosen()
    {
        return $this->belongsTo(DosenModel::class, 'dosen_id', 'dosen_id');
    }

    public function prestasi()
    {
        return $this->belongsTo(PrestasiModel::class, 'prestasi_id', 'prestasi_id');
    }

    protected $casts = [
        'status_validasi' => 'string',
    ];
}