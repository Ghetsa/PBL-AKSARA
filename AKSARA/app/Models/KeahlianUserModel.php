<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class KeahlianUserModel extends Model
{
    use HasFactory;

    protected $table = 'keahlian_user';
    protected $primaryKey = 'keahlian_user_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'bidang_id',
        'sertifikasi', // Path file sertifikat
        'nama_sertifikat',
        'lembaga_sertifikasi',
        'tanggal_perolehan_sertifikat',
        'tanggal_kadaluarsa_sertifikat',
        'status_verifikasi',
        'catatan_verifikasi',
    ];

    protected $casts = [
        'tanggal_perolehan_sertifikat' => 'date',
        'tanggal_kadaluarsa_sertifikat' => 'date',
    ];

    // Relasi ke bidang (Many-to-One)
    public function bidang()
    {
        return $this->belongsTo(BidangModel::class, 'bidang_id', 'bidang_id');
    }

    // Relasi ke user (Many-to-One)
    public function user()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    // Accessor untuk badge status_verifikasi
    public function getStatusVerifikasiBadgeAttribute()
    {
        // switch ($this->status_verifikasi) {
        //     case 'disetujui':
        //         return '<span class="badge bg-success">Disetujui</span>';
        //     case 'ditolak':
        //         return '<span class="badge bg-danger">Ditolak</span>';
        //     case 'pending':
        //     default:
        //         return '<span class="badge bg-warning text-dark">Pending</span>';
        // }
        $status = strtolower($this->status_verifikasi);
        $badgeClass = 'bg-light-secondary text-secondary'; // Default badge
        $label = ucfirst($status ?: 'Belum Diajukan'); // Fallback label

        switch ($status) {
            case 'disetujui':
                $badgeClass = 'bg-light-success text-success';
                $label = 'Disetujui';
                break;
            case 'pending':
                $badgeClass = 'bg-light-warning text-warning';
                $label = 'Menunggu';
                break;
            case 'ditolak':
                $badgeClass = 'bg-light-danger text-danger';
                $label = 'Ditolak';
                break;
        }
        return '<span class="badge ' . $badgeClass . ' px-2 py-1 fs-6">' . $label . '</span>';
    }

    // Accessor untuk link sertifikasi
    public function getSertifikasiUrlAttribute()
    {
        if ($this->sertifikasi && Storage::disk('public')->exists($this->sertifikasi)) {
            return asset('storage/' . $this->sertifikasi);
        }
        return null;
    }
}
