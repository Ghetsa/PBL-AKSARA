<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdiModel extends Model
{
    protected $table = 'program_studi';
    protected $primaryKey = 'prodi_id';
    public $timestamps = true;

    protected $fillable = [
        'nama',
    ];
}
