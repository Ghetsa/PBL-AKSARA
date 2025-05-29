<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LombaModel extends Model
{
    use HasFactory;

    protected $table = 'lomba';
    protected $primaryKey = 'lomba_id';
    public $incrementing = true;
    protected $keyType = 'int';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'nama_lomba',
        'pembukaan_pendaftaran',
        'kategori',
        'penyelenggara',
        'tingkat',
        'biaya',
        'link_pendaftaran',
        'link_penyelenggara',
        'batas_pendaftaran',
        'status_verifikasi',
        'catatan_verifikasi',
        'diinput_oleh',
        'poster',
    ];

    protected $casts = [
        'pembukaan_pendaftaran' => 'date',
        'batas_pendaftaran' => 'date',
        'biaya' => 'integer',
        'diinput_oleh' => 'integer',
        'kategori' => 'string',
        'tingkat' => 'string',
        'status_verifikasi' => 'string',
        'catatan_verifikasi' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function inputBy()
    {
        return $this->belongsTo(User::class, 'diinput_oleh', 'user_id');
    }

    public function pendaftar()
    {
        return $this->hasMany(PendaftaranLombaModel::class, 'lomba_id', 'lomba_id');
    }

    public function rekomendasiTerkait()
    {
        return $this->hasMany(RekomendasiLombaModel::class, 'lomba_id', 'lomba_id');
    }

    public function daftarHadiah()
    {
        return $this->hasMany(LombaHadiahModel::class, 'lomba_id', 'lomba_id');
    }

    public function getJumlahHadiahAttribute(): float
    {
        if ($this->relationLoaded('daftarHadiah')) {
            return (float) $this->daftarHadiah->sum('nominal_hadiah');
        }
        return 0.0;
    }

    public function getStatusPendaftaranDisplayAttribute(): string
    {
        $sekarang = Carbon::now();
        $pembukaan = $this->pembukaan_pendaftaran;
        $penutupan = $this->batas_pendaftaran;

        if ($pembukaan && $pembukaan->isFuture()) {
            return 'Segera Hadir';
        }

        if ($penutupan && $penutupan->isPast()) {
            return 'Tutup';
        }

        if ((!$pembukaan || $pembukaan->isPast() || $pembukaan->isToday()) && (!$penutupan || $penutupan->isFuture() || $penutupan->isToday())) {
            if ($pembukaan && $penutupan && $sekarang->between($pembukaan, $penutupan->endOfDay())) {
                return 'Buka';
            } elseif ($pembukaan && !$penutupan && ($pembukaan->isPast() || $pembukaan->isToday())) {
                return 'Buka';
            } elseif (!$pembukaan && $penutupan && ($penutupan->isFuture() || $penutupan->isToday())) {
                return 'Buka';
            } elseif (!$pembukaan && !$penutupan) {
                return 'Buka';
            }
        }

        if ($penutupan && $penutupan->isPast()) {
            return 'Tutup';
        }

        return 'Informasi Tidak Lengkap';
    }

    public function detailBidang()
    {
        return $this->hasMany(LombaDetailModel::class, 'lomba_id', 'lomba_id');
    }

    public function bidangTerkait()
    {
        return $this->hasManyThrough(
            BidangModel::class,
            LombaDetailModel::class,
            'lomba_id',
            'bidang_id',
            'lomba_id',
            'bidang_id'
        );
    }

    public function bidang()
    {
        return $this->belongsTo(BidangModel::class, 'bidang_id', 'id_bidang');
    }

    public function detailLomba()
    {
        return $this->hasMany(LombaDetailModel::class, 'lomba_id', 'lomba_id');
    }

    public function getStatusVerifikasiBadgeAttribute(): string
    {
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
        return '<span class="badge ' . $badgeClass . ' px-2 py-1">' . $label . '</span>';
    }
}
