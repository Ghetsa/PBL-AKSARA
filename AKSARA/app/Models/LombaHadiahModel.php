<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LombaHadiahModel extends Model
{
    use HasFactory;

    protected $table = 'lomba_hadiah';
    protected $primaryKey = 'lomba_hadiah_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'lomba_id',
        'hadiah',
    ];

    protected $casts = [
        'lomba_id' => 'integer',
        'hadiah' => 'string',
    ];

    public function lomba()
    {
        return $this->belongsTo(LombaModel::class, 'lomba_id', 'lomba_id');
    }
}
