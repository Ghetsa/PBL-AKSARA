@extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Dashboard Dosen')

@push('css')
    {{-- CSS untuk menyesuaikan tinggi dan tampilan gambar pada card --}}
    <style>
        .card-lomba .card-img-top, .card-prestasi .card-img-top {
            height: 180px;
            object-fit: cover; /* Memastikan gambar terpotong rapi, bukan penyok */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ $breadcrumb->title }}</h1>
        </div>

        {{-- Menampilkan 3 Kartu Statistik --}}
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jumlah Mahasiswa Bimbingan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahMahasiswaBimbingan }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Jumlah Prestasi Mahasiswa</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPrestasiKeseluruhan }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-award fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Lomba (Disetujui)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahLombaDisetujui }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-trophy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Akhir baris kartu statistik --}}
        
        <hr class="my-4">

        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-3">📢 Info Lomba Terkini</h4>
                <a href="{{ route('lomba.publik.index') }}" class="btn btn-sm btn-outline-primary mb-3">Lihat Semua Lomba</a>
            </div>
            @forelse ($infoLombaTerbaru as $lomba)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card card-lomba border-left-primary shadow h-100 py-2">
                        @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                            <img src="{{ asset('storage/'.$lomba->poster) }}" class="card-img-top" alt="Poster {{ $lomba->nama_lomba }}">
                        @else
                            <img src="{{ asset('default/6.png') }}" class="card-img-top" alt="Poster Default">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title text-primary font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($lomba->nama_lomba, 45) }}</h5>
                            <div class="text-xs mb-1">Penyelenggara: {{ $lomba->penyelenggara }}</div>
                            <div class="text-xs mb-2">Batas Daftar: <span class="font-weight-bold">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMM YYYY') : 'N/A' }}</span></div>
                            <a href="#" onclick="modalActionLomba('{{ route('lomba.publik.show_ajax', $lomba->lomba_id) }}', 'Detail Lomba', 'modalDetailLomba')" class="btn btn-primary btn-sm mt-2">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">Tidak ada info lomba terkini.</p>
                </div>
            @endforelse
        </div>
        <hr class="my-4">

        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-3">🌟 Prestasi Mahasiswa Bimbingan Anda</h4>
                 <a href="{{ route('bimbingan.index') }}" class="btn btn-sm btn-outline-success mb-3">Lihat Semua Bimbingan</a>
            </div>
            @forelse ($prestasiBimbingan as $prestasi)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card card-prestasi border-left-success shadow h-100 py-2">
                        @if($prestasi->bukti_prestasi && Storage::disk('public')->exists($prestasi->bukti_prestasi))
                            <img src="{{ asset('storage/'.$prestasi->bukti_prestasi) }}" class="card-img-top" alt="Bukti {{ $prestasi->nama_prestasi }}">
                        @else
                            <img src="{{ asset('default/8.png') }}" class="card-img-top" alt="Tidak Ada Bukti">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title text-success font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($prestasi->nama_prestasi, 45) }}</h5>
                            <div class="text-xs mb-1">Diraih oleh: <strong>{{ $prestasi->mahasiswa->user->nama ?? 'N/A' }}</strong></div>
                            <div class="text-xs mb-1">Penyelenggara: {{ $prestasi->penyelenggara_prestasi }}</div>
                            <div class="text-xs mb-1">Tingkat: {{ ucfirst($prestasi->tingkat_prestasi) }}</div>
                             {{-- Menggunakan route `prestasi.dosen.show_ajax` karena dosen yang melihat --}}
                            <a href="#" onclick="modalActionPrestasi('{{ route('prestasi.publik.show_ajax', $prestasi->prestasi_id) }}', 'Detail Prestasi', 'modalDetailPrestasi')" class="btn btn-success btn-sm mt-2">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">Belum ada catatan prestasi dari mahasiswa bimbingan Anda.</p>
                </div>
            @endforelse
        </div>
        <hr class="my-4">

        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-3">🏅 Prestasi Umum Mahasiswa</h4>
                <a href="{{ route('prestasi.dosen.index') }}" class="btn btn-sm btn-secondary mb-3">Lihat Semua Prestasi</a>
            </div>
            @forelse ($prestasiKeseluruhan as $prestasi)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card card-prestasi border-left-secondary shadow h-100 py-2">
                        @if($prestasi->bukti_prestasi && Storage::disk('public')->exists($prestasi->bukti_prestasi))
                            <img src="{{ asset('storage/'.$prestasi->bukti_prestasi) }}" class="card-img-top" alt="Bukti {{ $prestasi->nama_prestasi }}">
                        @else
                            <img src="{{ asset('default/7.png') }}" class="card-img-top" alt="Tidak Ada Bukti">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title text-secondary font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($prestasi->nama_prestasi, 45) }}</h5>
                            <div class="text-xs mb-1">Diraih oleh: {{ $prestasi->mahasiswa->user->nama ?? 'N/A' }} ({{ $prestasi->mahasiswa->prodi->nama_prodi ?? 'N/A' }})</div>
                            <div class="text-xs mb-1">Penyelenggara: {{ $prestasi->penyelenggara_prestasi }}</div>
                            <div class="text-xs mb-1">Tingkat: {{ ucfirst($prestasi->tingkat_prestasi) }}</div>
                            <a href="#" onclick="modalActionPrestasi('{{ route('prestasi.dosen.show_ajax', $prestasi->prestasi_id) }}', 'Detail Prestasi', 'modalDetailPrestasi')" class="btn btn-secondary btn-sm mt-2">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">Belum ada prestasi umum untuk ditampilkan.</p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- [BARU] MODAL DEFINITIONS --}}
    <div class="modal fade" id="modalDetailLomba" tabindex="-1" aria-labelledby="modalDetailLombaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content"></div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailPrestasi" tabindex="-1" aria-labelledby="modalDetailPrestasiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content"></div>
        </div>
    </div>
@endsection

@push('js')
    {{-- Tidak perlu chart.js untuk halaman ini, bisa dihapus jika tidak ada grafik --}}
    {{-- <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script> --}}

    <script>
        // [BARU] Javascript untuk menampilkan modal detail (dicopy dari dashboard mahasiswa)
        
        // Fungsi untuk menampilkan modal detail LOMBA
        function modalActionLomba(url, title = 'Detail', modalId = 'modalDetailLomba') {
            const targetModal = $(`#${modalId}`);
            const targetModalContent = targetModal.find('.modal-content');
            
            targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat...</p></div>');
            
            const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
            modalInstance.show();

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    targetModalContent.html(response);
                },
                error: function (xhr) { 
                    let msg = xhr.responseJSON?.message ?? 'Gagal memuat konten.';
                    targetModalContent.html(`<div class="modal-header"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${msg}</p></div>`);
                }
            });
        }

        // Fungsi untuk menampilkan modal detail PRESTASI
        function modalActionPrestasi(url, title = 'Detail Prestasi', modalId = 'modalDetailPrestasi') {
            const targetModal = $(`#${modalId}`);
            const targetModalContent = targetModal.find('.modal-content');

            targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat...</p></div>');
            
            const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
            modalInstance.show();
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    targetModalContent.html(response);
                },
                error: function (xhr) { 
                    let msg = xhr.responseJSON?.message ?? 'Gagal memuat konten.';
                    targetModalContent.html(`<div class="modal-header"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${msg}</p></div>`);
                }
            });
        }
    </script>
@endpush