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
            <a href="{{ route('dosen.notifikasi.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>

            {{-- ============================================= --}}
            {{-- TAMBAHAN: Tombol Cek Bimbingan untuk Dosen --}}
            {{-- ============================================= --}}
            @if ($notif->type === 'prestasi' && Str::contains($notif->judul, 'Bimbingan'))
                <a href="{{ route('bimbingan.index') }}" class="btn btn-info">
                    <i class="bx bx-user-check"></i> Lihat Daftar Bimbingan
                </a>
            @endif
        </div>
    </div>
</div>
@endsection