<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DosenModel extends Model
{
    use HasFactory;

    protected $table = 'dosen';
    protected $primaryKey = 'dosen_id';
    public $timestamps = false;


    protected $fillable = [
        'user_id',
        'keahlian_id',
        'nip',
    ];

    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function bidang()
    {
        return $this->belongsTo(BidangModel::class, 'keahlian_id', 'keahlian_id');
    }

    public function bimbinganMahasiswa()
    {
        return $this->hasMany(MahasiswaBimbinganModel::class, 'dosen_id', 'dosen_id');
    }

    public function pendaftaranLomba()
    {
        return $this->hasMany(PendaftaranLombaModel::class, 'dosen_pembimbing_id', 'dosen_id');
    }

     public function feedback()
     {
         return $this->hasMany(FeedbackDosenModel::class, 'dosen_id', 'dosen_id');
     }
}