<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LombaDetailModel extends Model
{
    use HasFactory;

    protected $table = 'lomba_detail';
    protected $primaryKey = 'lomba_detail_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'lomba_id',
        'bidang_id',
        'kategori',
    ];

    protected $casts = [
        'lomba_id' => 'integer',
        'bidang_id' => 'integer',
        'kategori' => 'string',
    ];

    public function lomba()
    {
        return $this->belongsTo(LombaModel::class, 'lomba_id', 'lomba_id');
    }

    public function bidang()
    {
        return $this->belongsTo(BidangModel::class, 'bidang_id', 'bidang_id');
    }
}
