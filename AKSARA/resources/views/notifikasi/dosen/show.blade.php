@php
    $iconClass = 'fas fa-bell'; // Default
    $iconColor = 'text-secondary';
    if (Str::contains($notif->type, 'lomba')) {
        $iconClass = 'fas fa-award';
        $iconColor = 'text-warning';
    } elseif (Str::contains($notif->type, 'prestasi')) {
        $iconClass = 'fas fa-trophy';
        $iconColor = 'text-success';
    } elseif (Str::contains($notif->type, 'keahlian')) {
        $iconClass = 'fas fa-star';
        $iconColor = 'text-info';
    }
@endphp

<div class="modal-header">
    <h5 class="modal-title d-flex align-items-center">
        <i class="{{ $iconClass }} {{ $iconColor }} me-2"></i>
        Detail Notifikasi
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <h6 class="fw-bold">{{ $notif->judul }}</h6>
    <p class="text-muted"><small>Diterima pada: {{ $notif->created_at->format('d F Y, H:i') }} WIB</small></p>
    <hr>
    <p>{{ $notif->isi }}</p>
</div>
<div class="modal-footer d-flex justify-content-end">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
    
    @php
        $verifikasiRoute = null;
        // Tentukan route tujuan berdasarkan tipe notifikasi
        switch ($notif->type) {
            case 'lomba':
                $verifikasiRoute = route('lomba.dsn.histori.index');
                break;
            case 'prestasi':
                $verifikasiRoute = route('bimbingan.index');
                break;
            // case 'keahlian':
            //     $verifikasiRoute = route('keahlian_user.admin.index');
            //     break;
        }
    @endphp

    @if (Str::contains($notif->judul, ['Baru', 'pengajuan', 'Prestasi']))
        <a href="{{ $verifikasiRoute ?? '#' }}" class="btn btn-primary">
            <i class="fas fa-clipboard-check me-1"></i> Cek Halaman Verifikasi
        </a>
    @elseif (Str::contains($notif->judul, ['Lomba', 'lomba']))
        <a href="{{ $verifikasiRoute ?? '#' }}" class="btn btn-primary">
            <i class="fas fa-award me-1"></i> Cek Pengajuan Lomba
        </a>
    @endif

    @if ($notif->type === 'prestasi' && Str::contains($notif->judul, 'Bimbingan'))
        <a href="{{ route('bimbingan.index') }}" class="btn btn-info">
            <i class="bx bx-user-check"></i> Lihat Daftar Bimbingan
        </a>
    @endif
</div>

{{-- @extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Detail Notifikasi')

@section('content')
<div class="page-content">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ $notif->judul }}</h4>
            <small class="text-muted">Diterima pada: {{ $notif->created_at->format('d F Y, H:i') }} WIB</small>
        </div>
        <div class="card-body">
            <p>{{ $notif->isi }}</p>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="{{ route('dosen.notifikasi.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection --}}