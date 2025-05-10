<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeahlianModel extends Model
{
    protected $table = 'keahlian';
    protected $primaryKey = 'keahlian_id';
    // public $timestamps = false;

    protected $fillable = [
        'keahlian_nama'
    ];
}
