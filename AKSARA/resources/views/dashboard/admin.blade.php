@extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Dashboard Admin')

@push('css')
    {{-- CSS untuk menyesuaikan tinggi dan tampilan gambar pada card --}}
    <style>
        .card-lomba .card-img-top, .card-prestasi .card-img-top {
            height: 180px;
            object-fit: cover;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $breadcrumb->title }}</h1>
    </div>

    <div class="row">
        {{-- (Ini adalah bagian widget yang sudah ada di controller Anda) --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Lomba</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLomba }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Prestasi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPrestasi }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total User</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUser }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pengajuan Pending (Lomba)</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $lombaPengajuanPending }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-7">
            {{-- Chart JS untuk Tren Lomba --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Tren Pendaftaran Lomba (12 Bulan Terakhir)</h6></div>
                <div class="card-body"><canvas id="trenLombaChart"></canvas></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-5">
            {{-- Chart JS untuk Prestasi by Tingkat --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Prestasi Berdasarkan Tingkat</h6></div>
                <div class="card-body"><canvas id="prestasiByTingkatChart"></canvas></div>
            </div>
        </div>
    </div>

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
                        <div class="text-xs mb-2">Batas Daftar: <span class="font-weight-bold">{{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->isoFormat('D MMM YY') : 'N/A' }}</span></div>
                        {{-- Button ini mengarah ke modal edit/detail untuk admin --}}
                        <a href="#" onclick="modalAction('{{ route('admin.lomba.crud.edit_form_ajax', $lomba->lomba_id) }}', 'Detail Lomba', 'modalFormLomba')" class="btn btn-primary btn-sm mt-2">Lihat Detail</a>
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
                    @if($prestasi->bukti_prestasi && Storage::disk('public')->exists($prestasi->bukti_prestasi))
                        <img src="{{ asset('storage/'.$prestasi->bukti_prestasi) }}" class="card-img-top" alt="Bukti Prestasi">
                    @else
                        <img src="{{ asset('default/8.png') }}" class="card-img-top" alt="Tidak Ada Bukti">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title text-success font-weight-bold text-uppercase mb-1" style="font-size: 1.1rem;">{{ Str::limit($prestasi->nama_prestasi, 45) }}</h5>
                        <div class="text-xs mb-1">Diraih oleh: <strong>{{ $prestasi->mahasiswa->user->nama ?? 'N/A' }}</strong></div>
                        <div class="text-xs mb-1">Penyelenggara: {{ $prestasi->penyelenggara_prestasi }}</div>
                        <div class="text-xs mb-1">Tingkat: {{ ucfirst($prestasi->tingkat_prestasi) }}</div>
                        {{-- Button ini mengarah ke modal verifikasi untuk admin --}}
                        <a href="#" onclick="modalAction('{{ route('prestasi.admin.verify_form_ajax', $prestasi->prestasi_id) }}', 'Verifikasi Prestasi', 'modalFormPrestasi')" class="btn btn-success btn-sm mt-2">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><p class="text-center text-muted">Belum ada prestasi terbaru.</p></div>
        @endforelse
    </div>
</div>

{{-- [BARU] MODAL DEFINITIONS --}}
<div class="modal fade" id="modalFormLomba" tabindex="-1" aria-labelledby="modalFormLombaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>

<div class="modal fade" id="modalFormPrestasi" tabindex="-1" aria-labelledby="modalFormPrestasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>
@endsection

@push('js')
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script>
        // Skrip untuk Chart.js (sesuaikan dengan data dari controller)
        // Contoh untuk Doughnut Chart Prestasi
        var ctxPrestasi = document.getElementById("prestasiByTingkatChart");
        var prestasiByTingkatChart = new Chart(ctxPrestasi, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($prestasiByTingkat->keys()) !!},
                datasets: [{
                    data: {!! json_encode($prestasiByTingkat->values()) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                }],
            },
            options: { maintainAspectRatio: false, responsive: true }
        });

        // Contoh untuk Line Chart Tren Lomba
        var ctxTren = document.getElementById("trenLombaChart");
        var trenLombaChart = new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_values($labelsBulan)) !!},
                datasets: [{
                    label: "Jumlah Lomba",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    data: {!! json_encode(array_values($dataBulan)) !!},
                }],
            },
            options: { maintainAspectRatio: false, responsive: true }
        });


        // [BARU] Fungsi generik untuk menampilkan modal AJAX
        function modalAction(url, title = 'Detail', modalId = 'modalFormLomba') {
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