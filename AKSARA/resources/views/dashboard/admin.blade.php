@extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Dashboard Admin')

@push('css')
    {{-- [BARU] CSS untuk merapikan tampilan gambar pada card --}}
    <style>
        .card-lomba .card-img-top, .card-prestasi .card-img-top {
            height: 180px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        {{-- Bagian Widget Atas (TIDAK DIUBAH) --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ $breadcrumb->title }}</h1>
        </div>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Lomba (Semua)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLomba ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Lomba Aktif (Disetujui)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lombaAktif ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pengajuan Lomba Pending</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lombaPengajuanPending ?? 0 }}</div>
                                <a href="{{ route('admin.lomba.verifikasi.index') }}" class="stretched-link"></a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pengguna</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUser ?? 0 }}</div>
                                <div class="text-xs mt-1">Mhs: {{ $totalMahasiswa ?? 0 }} | Dosen: {{ $totalDosen ?? 0 }}</div>
                                <a href="{{ route('user.index') }}" class="stretched-link"></a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Prestasi (Semua)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPrestasi ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-trophy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Prestasi Disetujui</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $prestasiDisetujui ?? 0 }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-award fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Prestasi Pending Verifikasi</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $prestasiPending ?? 0 }}</div>
                                <a href="{{ route('prestasi.admin.index') }}?status_verifikasi=pending" class="stretched-link"></a>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bagian Grafik (TIDAK DIUBAH) --}}
        <div class="row">
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Lomba berdasarkan Tingkat</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="lombaByTingkatChart" width="300" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Distribusi Prestasi berdasarkan Tingkat</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-pie pt-4 pb-2">
                            <canvas id="prestasiByTingkatChart" width="300" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tren Banyak Lomba (6 Bulan Terakhir hingga 6 Bulan Mendatang)</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="height: 320px;">
                            <canvas id="lombaPerBulanChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Garis Pemisah --}}
        <hr class="my-4">

        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-3">📢 Informasi Lomba Terbaru</h4>
                <a href="{{ route('admin.lomba.crud.index') }}" class="btn btn-sm btn-outline-primary mb-3">Lihat Semua Lomba</a>
            </div>
            @forelse ($infoLombaTerbaru as $lomba)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card card-lomba border-left-primary shadow h-100 py-2">
                        @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                            <img src="{{ asset('storage/'.$lomba->poster) }}" class="card-img-top" alt="Poster Lomba">
                        @else
                            <img src="{{ asset('default/6.png') }}" class="card-img-top" alt="Poster Default">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title text-primary font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($lomba->nama_lomba, 45) }}</h5>
                            <div class="text-xs mb-1">Penyelenggara: {{ $lomba->penyelenggara }}</div>
                            <div class="text-xs mb-2">Batas Daftar: <span class="font-weight-bold">{{ $lomba->batas_pendaftaran ? \Carbon\Carbon::parse($lomba->batas_pendaftaran)->isoFormat('D MMM YY') : 'N/A' }}</span></div>
                            <a href="#" onclick="modalAction('{{ route('lomba.publik.show_ajax', $lomba->lomba_id) }}', 'Detail Lomba', 'modalForm')" class="btn btn-primary btn-sm mt-2">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p class="text-center text-muted">Tidak ada info lomba terkini.</p></div>
            @endforelse
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-3">🏅 Prestasi Terbaru Mahasiswa</h4>
                <a href="{{ route('prestasi.admin.index') }}" class="btn btn-sm btn-outline-success mb-3">Verifikasi Prestasi</a>
            </div>
            @forelse ($prestasiKeseluruhanTerbaru as $prestasi)
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card card-prestasi border-left-success shadow h-100 py-2">
                        @if($prestasi->file_bukti && Storage::disk('public')->exists($prestasi->file_bukti))
                            <img src="{{ asset('storage/'.$prestasi->file_bukti) }}" class="card-img-top" alt="Bukti Prestasi">
                        @else
                            <img src="{{ asset('default/8.png') }}" class="card-img-top" alt="Tidak Ada Bukti">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title text-success font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($prestasi->nama_prestasi, 45) }}</h5>
                            <div class="text-xs mb-1">Diraih oleh: <strong>{{ $prestasi->mahasiswa->user->nama ?? 'N/A' }}</strong></div>
                            <div class="text-xs mb-1">Penyelenggara: {{ $prestasi->penyelenggara }}</div>
                            <div class="text-xs mb-1">Tingkat: {{ ucfirst($prestasi->tingkat) }}</div>
                            <a href="#" onclick="modalAction('{{ route('prestasi.publik.show_ajax', $prestasi->prestasi_id) }}', 'Verifikasi Prestasi', 'modalForm')" class="btn btn-success btn-sm mt-2">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p class="text-center text-muted">Belum ada prestasi terbaru.</p></div>
            @endforelse
        </div>
    </div>

    {{-- [BARU] Modal generik untuk menampilkan konten AJAX --}}
    <div class="modal fade" id="modalForm" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content"></div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Skrip untuk Grafik (TIDAK DIUBAH) --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Grafik Lomba Berdasarkan Tingkat
            const ctxLomba = document.getElementById("lombaByTingkatChart");
            if (ctxLomba) {
                new Chart(ctxLomba, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($lombaByTingkat->keys()) !!},
                        datasets: [{
                            data: {!! json_encode($lombaByTingkat->values()) !!},
                            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: { callbacks: { label: (c) => c.label + ": " + c.parsed } }
                        }
                    }
                });
            }

            // Grafik Prestasi Berdasarkan Tingkat
            const ctxPrestasi = document.getElementById("prestasiByTingkatChart");
            if (ctxPrestasi) {
                new Chart(ctxPrestasi, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($prestasiByTingkat->keys()) !!},
                        datasets: [{
                            data: {!! json_encode($prestasiByTingkat->values()) !!},
                            backgroundColor: ['#1cc88a', '#36b9cc', '#4e73df', '#f6c23e', '#e74a3b'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' },
                            tooltip: { callbacks: { label: (c) => c.label + ": " + c.parsed } }
                        }
                    }
                });
            }

            // Grafik Tren Lomba Per Bulan
            const labelsBulan = @json(array_values($labelsBulan));
            const dataBulan = @json(array_values($dataBulan));
            const ctxBulan = document.getElementById("lombaPerBulanChart");

            if (ctxBulan) {
                new Chart(ctxBulan, {
                    type: 'line',
                    data: {
                        labels: labelsBulan,
                        datasets: [{
                            label: "Jumlah Lomba",
                            data: dataBulan,
                            borderColor: "#4e73df",
                            backgroundColor: "rgba(78, 115, 223, 0.05)",
                            fill: true,
                            tension: 0.4,
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true }
                        },
                        plugins: { legend: { display: false } }
                    }
                });
            }
        });
    </script>
    
    {{-- [BARU] Skrip untuk Modal AJAX --}}
    <script>
        function modalAction(url, title = 'Detail', modalId = 'modalForm') {
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