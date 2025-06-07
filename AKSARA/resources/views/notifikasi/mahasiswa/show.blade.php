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
            <a href="{{ route('mahasiswa.notifikasi.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>

            {{-- ================================================= --}}
            {{-- TAMBAHAN: Tombol Lihat Pengajuan untuk Mahasiswa --}}
            {{-- ================================================= --}}
            @php
                $detailRoute = null;
                $detailParams = [];
                // Tentukan route tujuan berdasarkan tipe notifikasi
                switch ($notif->type) {
                    case 'lomba':
                        $detailRoute = 'lomba.mhs.show_form';
                        $detailParams = ['id' => $notif->lomba->lomba_id]; // Mengambil ID dari relasi
                        break;
                    case 'prestasi':
                        // Asumsi Anda punya route untuk detail prestasi mahasiswa
                        // Jika tidak ada, bisa diarahkan ke halaman list
                        if (Route::has('prestasi.mahasiswa.show_ajax')) {
                             // Anda bisa membuat logic untuk membuka modal dari sini jika mau
                        }
                        break;
                    case 'keahlian':
                        // Asumsi Anda punya route untuk detail keahlian
                        if (Route::has('keahlian_user.show_ajax')) {
                            // Anda bisa membuat logic untuk membuka modal dari sini jika mau
                        }
                        break;
                }
            @endphp

            @if ($detailRoute && !empty($detailParams))
                <a href="{{ route($detailRoute, $detailParams) }}" class="btn btn-info">
                    <i class="bx bx-file"></i> Lihat Detail Pengajuan
                </a>
            @endif
        </div>
    </div>
</div>
@endsection