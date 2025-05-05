<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdiModel extends Model
{
    protected $table = 'prodi';
    protected $primaryKey = 'prodi_id';
    public $timestamps = true;

    protected $fillable = [
        'nama',
    ];
}
