<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeModel extends Model
{
    protected $table = 'periode';
    protected $primaryKey = 'periode_id';
    public $timestamps = true;

    protected $fillable = [
        'semester',
        'tahun_akademik',
    ];
}
