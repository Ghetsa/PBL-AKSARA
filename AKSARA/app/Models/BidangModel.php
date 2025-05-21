<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BidangModel extends Model
{
  protected $table = 'bidang';
  protected $primaryKey = 'bidang_id';
  public $timestamps = true;

  protected $fillable = [
    'bidang_nama',
  ];

  // Daftar pilihan bidang yang tersedia (statis)
  public static function getPilihanBidang()
  {
    return self::orderBy('bidang_nama')->pluck('bidang_nama')->toArray();
  }
  public static function getPilihanMinat()
  {
    return self::orderBy('bidang_nama')->pluck('bidang_nama')->toArray();
  }

  // Relasi ke User (Many-to-Many)
  public function users()
  {
    return $this->belongsToMany(UserModel::class, 'keahlian_user', 'bidang_id', 'user_id')
      ->withPivot('keahlian_user_id', 'sertifikasi', 'status_verifikasi', 'catatan_verifikasi')
      ->withTimestamps();
  }

  // Relasi ke User (Many-to-Many)
  public function user()
  {
    return $this->belongsToMany(UserModel::class, 'minat_user', 'bidang_id', 'user_id')
      ->withTimestamps();
  }

}
