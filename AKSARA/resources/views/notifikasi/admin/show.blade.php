@extends('layouts.template')

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
            <a href="{{ route('admin.notifikasi.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>

            {{-- ============================================= --}}
            {{-- TAMBAHAN: Tombol Cek Verifikasi untuk Admin --}}
            {{-- ============================================= --}}
            @php
                $verifikasiRoute = null;
                // Tentukan route tujuan berdasarkan tipe notifikasi
                switch ($notif->type) {
                    case 'lomba':
                        $verifikasiRoute = route('admin.lomba.verifikasi.index');
                        break;
                    case 'prestasi':
                        $verifikasiRoute = route('prestasi.admin.index');
                        break;
                    case 'keahlian':
                        $verifikasiRoute = route('keahlian_user.admin.index');
                        break;
                }
            @endphp

            {{-- Tampilkan tombol hanya jika notifikasi adalah tentang pengajuan baru --}}
            @if (Str::contains($notif->judul, 'Baru') && $verifikasiRoute)
                <a href="{{ $verifikasiRoute }}" class="btn btn-primary">
                    <i class="bx bx-shield-quarter"></i> Cek Halaman Verifikasi
                </a>
            @endif
        </div>
    </div>
</div>
@endsection