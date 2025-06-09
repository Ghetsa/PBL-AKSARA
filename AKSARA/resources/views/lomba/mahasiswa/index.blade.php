@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Informasi & Rekomendasi Lomba')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .form-label-group {
        margin-bottom: 0.5rem;
    }
    .slider-container {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .weight-value {
        font-weight: bold;
        min-width: 30px; /* Agar tampilan tidak meloncat saat angka berubah */
        display: inline-block;
        text-align: right;
    }
    #totalBobotText {
        font-weight: bold;
    }
    .bobot-warning {
        color: #dc3545; /* Bootstrap danger color */
        font-weight: bold;
    }
    .card-title-small {
        font-size: 1rem;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $breadcrumb->title ?? 'Daftar Lomba' }}</h6>
                </div>
                <div class="card-body">
                    {{-- Tombol Toggle untuk Form Bobot Kriteria --}}
                    <div class="mb-3 text-end">
                        <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBobotKriteria" aria-expanded="false" aria-controls="collapseBobotKriteria">
                            <i class="fas fa-cogs me-1"></i> Atur Prioritas Rekomendasi Lomba
                        </button>
                    </div>

                    {{-- Form Bobot Kriteria (Collapsible) --}}
                    <div class="collapse mb-4" id="collapseBobotKriteria">
                        <div class="card card-body border-info">
                            <h5 class="card-title card-title-small">Sesuaikan Prioritas Kriteria Rekomendasi</h5>
                            <p class="card-text small text-muted">Geser slider untuk menentukan seberapa penting setiap kriteria. Total bobot harus 100.</p>
                            <form id="formBobotKriteria" class="mt-2">
                                @foreach($kriteriaUntukBobot as $key => $label)
                                <div class="form-label-group row align-items-center mb-1">
                                    <label for="bobot_{{ $key }}" class="col-sm-4 col-form-label col-form-label-sm pe-0">{{ $label }}:</label>
                                    <div class="col-sm-6 slider-container">
                                        <input type="range" class="form-range bobot-slider" id="bobot_{{ $key }}" data-kriteria="{{ $key }}" min="0" max="50" value="{{ $defaultBobotView[$key] ?? 20 }}" step="5">
                                    </div>
                                    <div class="col-sm-2 ps-1">
                                        <span class="weight-value" id="value_{{ $key }}">{{ $defaultBobotView[$key] ?? 20 }}</span>
                                    </div>
                                </div>
                                @endforeach
                                <div class="row mt-3">
                                    <div class="col-sm-4"><strong>Total Bobot:</strong></div>
                                    <div class="col-sm-8"><strong id="totalBobotText">100</strong> <span id="bobotWarningText" class="small bobot-warning"></span></div>
                                </div>
                                <div class="mt-3">
                                    <button type="button" id="resetBobotBtn" class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-undo me-1"></i>Reset Bobot</button>
                                    <button type="button" id="terapkanBobotBtn" class="btn btn-sm btn-success">
                                        <i class="fas fa-check me-1"></i> Terapkan & Lihat Rekomendasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    {{-- Form Filter Pencarian Biasa --}}
                    <form id="filterFormLomba" class="row gx-3 gy-2 align-items-center mb-4">
                        <div class="col-sm-12 col-md-5">
                            <label class="form-label visually-hidden" for="search_nama">Cari Nama Lomba</label>
                            <input type="text" class="form-control form-control-sm" id="search_nama" placeholder="Cari nama lomba...">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label class="form-label visually-hidden" for="filter_status">Status Pendaftaran</label>
                            <select class="form-select form-select-sm" id="filter_status">
                                <option value="">Semua Status Pendaftaran</option>
                                <option value="buka">Buka</option>
                                <option value="tutup">Tutup</option>
                                <option value="segera hadir">Segera Hadir</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <button type="submit" class="btn btn-sm btn-primary w-100"><i class="fas fa-search me-1"></i> Cari Lomba</button>
                        </div>
                    </form>
                    <div id="infoRekomendasi" class="alert alert-info alert-dismissible fade show d-none" role="alert">
                        <i class="fas fa-info-circle me-2"></i>Menampilkan rekomendasi lomba berdasarkan preferensi Anda. Untuk pencarian biasa, gunakan filter di atas dan klik "Cari Lomba".
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <table class="table table-bordered table-hover dt-responsive nowrap" id="dataTableLomba" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th>Nama Lomba</th>
                                <th>Penyelenggara</th>
                                <th>Bidang</th>
                                <th>Tingkat</th>
                                <th>Batas Daftar</th>
                                <th class="text-center">Biaya</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" id="kolomSkorRekomendasi" style="display:none;">Skor Rekomendasi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Detail Lomba Publik --}}
<div class="modal fade" id="modalDetailLombaPublik" tabindex="-1" aria-labelledby="modalDetailLombaPublikLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal fade" id="modalDetailHitungan" tabindex="-1" aria-labelledby="modalDetailHitunganLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" id="modalDetailHitunganContent"></div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var dtLomba;
    var isRekomendasiActive = false;
    // Simpan bobot default dari Blade untuk reset
    const defaultBobotView = @json($defaultBobotView ?? []);

    // Pastikan fungsi ini didefinisikan secara global atau di dalam scope yang benar
    if (typeof modalActionLomba === 'undefined') {
        function modalActionLomba(url, title = 'Detail Lomba', modalId = 'modalDetailLombaPublik') {
            const targetModal = $(`#${modalId}`);
            const targetModalContent = targetModal.find('.modal-content');
            targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat...</p></div>');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
            modalInstance.show();
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    targetModalContent.html(response);
                },
                error: function(xhr) {
                    let msg = xhr.responseJSON?.message ?? 'Gagal memuat konten.';
                    targetModalContent.html(`<div class="modal-header bg-danger text-white"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${msg}</p></div>`);
                }
            });
        }
    }

    function updateTotalBobotDisplay() {
        let total = 0;
        $('.bobot-slider').each(function() {
            total += parseInt($(this).val());
        });
        $('#totalBobotText').text(total); // Hanya angka, tanpa %
        if (total !== 100) {
            $('#totalBobotText').addClass('bobot-warning');
            $('#bobotWarningText').text('(Total harus 100)');
            $('#terapkanBobotBtn').prop('disabled', true);
            $('#lihatDetailLengkapBtn').prop('disabled', true); // Nonaktifkan juga tombol detail
        } else {
            $('#totalBobotText').removeClass('bobot-warning');
            $('#bobotWarningText').text('');
            $('#terapkanBobotBtn').prop('disabled', false);
            $('#lihatDetailLengkapBtn').prop('disabled', false); // Aktifkan juga tombol detail
        }
    }

    $(document).ready(function() {
        $('.bobot-slider').on('input', function() {
            $('#value_' + $(this).data('kriteria')).text($(this).val());
            updateTotalBobotDisplay();
        });
        updateTotalBobotDisplay();

        $('#resetBobotBtn').on('click', function() {
            $('.bobot-slider').each(function() {
                const kriteriaKey = $(this).data('kriteria');
                const defaultValue = defaultBobotView[kriteriaKey] !== undefined ? defaultBobotView[kriteriaKey] : 20; // Sesuaikan default jika tidak ada di defaultBobotView
                $(this).val(defaultValue);
                $('#value_' + kriteriaKey).text(defaultValue);
            });
            updateTotalBobotDisplay();
            if (isRekomendasiActive) {
                isRekomendasiActive = false;
                dtLomba.column('moora_score:name').visible(false); // Menggunakan nama kolom yang ditentukan di DataTables
                $('#infoRekomendasi').addClass('d-none');
                dtLomba.ajax.reload();
                Swal.fire({
                    icon: 'info',
                    title: 'Mode Rekomendasi Dinonaktifkan',
                    text: 'Menampilkan semua lomba.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });

        dtLomba = $('#dataTableLomba').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('lomba.getList') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.search_nama = $('#search_nama').val();
                    d.filter_status = $('#filter_status').val();
                    d.rekomendasi = isRekomendasiActive ? '1' : '0';
                    if (isRekomendasiActive) {
                        d.weights = {};
                        $('.bobot-slider').each(function() {
                            // Kirim bobot sebagai desimal (nilai slider / 100)
                            d.weights[$(this).data('kriteria')] = parseInt($(this).val()) / 100;
                        });
                    }
                },
                error: function(xhr, error, thrown) {
                    console.error("DataTables AJAX error: ", xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal memuat data lomba. Silakan coba lagi atau hubungi admin.'
                    });
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'lomba.nama_lomba' },
                { data: 'penyelenggara', name: 'lomba.penyelenggara' },
                { data: 'bidang_display', name: 'bidang_display', orderable: false, searchable: false },
                { data: 'tingkat', name: 'lomba.tingkat', className: 'text-center' },
                { data: 'batas_pendaftaran', name: 'lomba.batas_pendaftaran', className: 'text-center' },
                { data: 'biaya_display', name: 'lomba.biaya', className: 'text-center' },
                { data: 'status_display', name: 'status_display', className: 'text-center', orderable: false, searchable: false },
                {
                    data: 'moora_score',
                    name: 'moora_score', // Penting: pastikan nama kolom ini sesuai dengan yang kamu definisikan di DataTables
                    className: 'text-center',
                    visible: false,
                    orderable: true,
                    render: function(data, type, row) {
                        if (isRekomendasiActive && data !== null) {
                            // Gunakan kelas 'btn-detail-hitungan' yang sama dengan tombol di kolom 'aksi'
                            // Ini agar handler klik di bawah bisa menangkapnya
                            return `<a href="#" class="detail-skor-link btn-detail-hitungan fw-bold" data-lomba-id="${row.lomba_id}">${parseFloat(data).toFixed(4)}</a>`;
                        }
                        return data;
                    }
                },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            order: [[5, 'asc']], // Default order by batas_pendaftaran
            fnDrawCallback: function (oSettings) {
                if (isRekomendasiActive && oSettings.fnRecordsDisplay() == 0 && oSettings.aiDisplay.length === 0 && !oSettings.oAjaxData.sSearch) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Rekomendasi',
                        text: 'Tidak ditemukan lomba yang sesuai dengan preferensi bobot Anda saat ini.',
                        timer: 3000,
                        showConfirmButton: true
                    });
                }
            }
        });

        $('#terapkanBobotBtn').on('click', function() {
            if (parseInt($('#totalBobotText').text()) !== 100) {
                Swal.fire('Peringatan', 'Total bobot kriteria harus 100 untuk menerapkan rekomendasi.', 'warning');
                return;
            }
            isRekomendasiActive = true;
            $('#infoRekomendasi').removeClass('d-none');
            $('#filterFormLomba').trigger('reset'); // Reset form filter lainnya
            $('#search_nama').val('');
            $('#filter_status').val('');

            dtLomba.column('moora_score:name').visible(true); // Tampilkan kolom skor
            dtLomba.order([dtLomba.column('moora_score:name').index(), 'desc']).draw(); // Order by skor desc
            Swal.fire({
                icon: 'info',
                title: 'Mode Rekomendasi Aktif',
                text: 'Mencari lomba berdasarkan prioritas Anda...',
                timer: 1500,
                showConfirmButton: false
            });
        });

        $('#filterFormLomba').on('submit', function(e) {
            e.preventDefault();
            if (isRekomendasiActive) {
                isRekomendasiActive = false; // Nonaktifkan jika sedang mencari
                $('#infoRekomendasi').addClass('d-none');
                Swal.fire({
                    icon: 'info',
                    title: 'Mode Rekomendasi Dinonaktifkan',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
            dtLomba.column('moora_score:name').visible(false); // Sembunyikan kolom skor saat filter biasa
            dtLomba.order([dtLomba.column('batas_pendaftaran:name').index(), 'asc']).draw(); // Kembalikan order default
        });

        // =========================================================================
        // === SCRIPT UNTUK TOMBOL "LIHAT DETAIL LENGKAP" (MEMBUKA DI TAB BARU) ===
        // =========================================================================
        $('#lihatDetailLengkapBtn').on('click', function(e) {
            e.preventDefault();

            if (parseInt($('#totalBobotText').text()) !== 100) {
                Swal.fire('Peringatan', 'Total bobot kriteria harus 100.', 'warning');
                return;
            }

            const weights = {};
            $('.bobot-slider').each(function() {
                // Pastikan bobot dikirim sebagai desimal (nilai slider / 100)
                weights[$(this).data('kriteria')] = parseInt($(this).val()) / 100;
            });

            const params = new URLSearchParams();
            for (const key in weights) {
                params.append(`weights[${key}]`, weights[key]);
            }

            // Memanggil rute yang baru (details.all) yang tidak butuh ID
            const url = `{{ route('lomba.mhs.details.all') }}?${params.toString()}`;
            window.open(url, '_blank'); // Membuka di tab baru
        });


        // =========================================================================
        // === SCRIPT YANG DIPERBAIKI UNTUK TOMBOL "HITUNGAN" DI SETIAP BARIS ===
        // === (MEMBUKA DETAIL DI MODAL) ===
        // =========================================================================
        $('#dataTableLomba tbody').on('click', '.btn-detail-hitungan', function(e) {
            e.preventDefault();
            const lombaId = $(this).data('lomba-id');
            const modalContent = $('#modalDetailHitunganContent'); // Pastikan ini ID dari div di dalam modal-dialog
            const modalTitle = 'Detail Perhitungan MOORA Lomba'; // Judul modal

            modalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat detail perhitungan...</p></div>');
            const bsModal = new bootstrap.Modal(document.getElementById('modalDetailHitungan')); // Pastikan ini ID dari modal div utama
            bsModal.show();

            // Ambil bobot kustom dari slider/input di halaman utama
            const weights = {};
            $('.bobot-slider').each(function() {
                // Kirim bobot sebagai desimal (nilai slider / 100)
                weights[$(this).data('kriteria')] = parseInt($(this).val()) / 100;
            });
            const params = new URLSearchParams();
            for (const key in weights) {
                params.append(`weights[${key}]`, weights[key]);
            }

            // Panggil rute showMooraDetails dengan lombaId dan bobot kustom
            $.ajax({
                // Gunakan route() helper dengan parameter ID
                url: `{{ route('lomba.mhs.details', ['id' => 'TEMP_ID']) }}`.replace('TEMP_ID', lombaId) + `?${params.toString()}`,
                method: 'GET',
                success: function(response) {
                    // Asumsi response adalah HTML dari view moora_details
                    modalContent.html(response);
                    // Jika kamu ingin mengubah judul modal setelah loading
                    $('#modalDetailHitungan .modal-title').text(modalTitle);
                },
                error: function(xhr, status, error) {
                    console.error("Terjadi kesalahan:", xhr.responseText);
                    let errorMessage = xhr.responseJSON?.message ?? 'Gagal memuat detail perhitungan. Silakan coba lagi.';
                    modalContent.html(`<div class="modal-header bg-danger text-white"><h5 class="modal-title">${modalTitle}</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${errorMessage}</p></div>`);
                }
            });
        });
    });
</script>
@endpush

{{-- @extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Informasi & Rekomendasi Lomba')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .priority-list-container {
        border: 1px solid #eee;
        padding: 15px;
        border-radius: 0.375rem;
        background-color: #f8f9fa;
    }

    .priority-slot {
        background-color: #fff;
        border: 1px dashed #ccc;
        border-radius: 0.25rem;
        padding: 10px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .priority-slot .kriteria-item {
        cursor: grab;
        padding: 8px 12px;
        background-color: #e9ecef;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        width: 100%;
        /* Agar item mengisi slot */
        text-align: center;
    }

    .priority-slot .priority-label {
        font-weight: bold;
        margin-right: 10px;
        color: #007bff;
        /* Bootstrap primary color */
    }

    .kriteria-bank {
        border: 1px solid #ddd;
        padding: 10px;
        min-height: 50px;
        /* Tinggi minimal untuk area drop */
        background-color: #fff;
        border-radius: 0.25rem;
    }

    .kriteria-bank .kriteria-item {
        margin-bottom: 5px;
        display: block;
        /* Agar setiap item di baris baru jika di bank */
    }

    .gu-mirror {
        /* Styling untuk elemen yang di-drag (dari dragula.js, contoh) */
        position: fixed !important;
        margin: 0 !important;
        z-index: 9999 !important;
        opacity: 0.8;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=80)";
        filter: alpha(opacity=80);
    }

    .gu-hide {
        display: none !important;
    }

    .gu-unselectable {
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
        user-select: none !important;
    }

    .gu-transit {
        opacity: 0.5;
        -ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";
        filter: alpha(opacity=50);
    }

    .bobot-info {
        font-size: 0.85em;
        color: #6c757d;
    }

    .handle {
        cursor: grab;
        margin-right: 8px;
        color: #adb5bd;
    }

    .handle:hover {
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $breadcrumb->title ?? 'Daftar Lomba' }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-end">
                        <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapsePrioritasKriteria" aria-expanded="false"
                            aria-controls="collapsePrioritasKriteria">
                            <i class="fas fa-sort-amount-down me-1"></i> Atur Prioritas Rekomendasi
                        </button>
                    </div>

                    <div class="collapse mb-4" id="collapsePrioritasKriteria">
                        <div class="card card-body border-info">
                            <h5 class="card-title card-title-small">Urutkan Kriteria Rekomendasi</h5>
                            <p class="card-text small text-muted">Seret dan lepas kriteria ke slot prioritas di bawah.
                                Kriteria di slot "Prioritas 1" akan memiliki bobot tertinggi.</p>

                            <div class="row mt-2">
                                <div class="col-md-7">
                                    <h6>Urutan Prioritas Anda:</h6>
                                    <div id="prioritySlotsContainer">
                                        @php
                                        $bobotPosisi = [ 1 => 30, 2 => 25, 3 => 20, 4 => 10, 5 => 10, 6 => 5 ]; //
                                        Persentase
                                        @endphp
                                        @foreach($bobotPosisi as $pos => $bobot)
                                        <div class="priority-slot mb-2" data-posisi="{{ $pos }}">
                                            <span class="priority-label"><i class="fas fa-list-ol me-1"></i> Prioritas
                                                {{ $pos }}:</span>
                                            <div class="kriteria-dropzone flex-grow-1"
                                                style="min-height: 40px; border: 1px dashed #ced4da; border-radius: .25rem; padding: 5px;">
                                            </div>
                                            <span class="bobot-info ms-2">({{ $bobot }}%)</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <h6>Pilihan Kriteria (Seret dari sini):</h6>
                                    <div id="kriteriaBank" class="kriteria-bank p-2">
                                        @foreach($kriteriaList as $key => $label)
                                        <div class="kriteria-item" data-kriteria-key="{{ $key }}">
                                            <i class="fas fa-grip-vertical handle me-2"></i>{{ $label }}
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="button" id="resetUrutanBtn"
                                    class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-undo me-1"></i>Reset
                                    Urutan</button>
                                <button type="button" id="terapkanUrutanBtn" class="btn btn-sm btn-success">
                                    <i class="fas fa-check me-1"></i> Terapkan & Lihat Rekomendasi
                                </button>
                            </div>
                        </div>
                    </div>

                    <form id="filterFormLomba" class="row gx-3 gy-2 align-items-center mb-4">
                        <div class="col-sm-12 col-md-5">
                            <label class="form-label visually-hidden" for="search_nama">Cari Nama Lomba</label>
                            <input type="text" class="form-control form-control-sm" id="search_nama"
                                placeholder="Cari nama lomba...">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label class="form-label visually-hidden" for="filter_status">Status Pendaftaran</label>
                            <select class="form-select form-select-sm" id="filter_status">
                                <option value="">Semua Status Pendaftaran</option>
                                <option value="buka">Buka</option>
                                <option value="tutup">Tutup</option>
                                <option value="segera hadir">Segera Hadir</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <button type="submit" class="btn btn-sm btn-primary w-100"><i
                                    class="fas fa-search me-1"></i> Cari Lomba</button>
                        </div>
                    </form>
                    <div id="infoRekomendasi" class="alert alert-info alert-dismissible fade show d-none" role="alert">
                        <i class="fas fa-info-circle me-2"></i>Menampilkan rekomendasi lomba berdasarkan urutan
                        prioritas kriteria Anda.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <table class="table table-bordered table-hover dt-responsive nowrap" id="dataTableLomba"
                        style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th>Nama Lomba</th>
                                <th>Penyelenggara</th>
                                <th>Bidang</th>
                                <th>Tingkat</th>
                                <th>Batas Daftar</th>
                                <th class="text-center">Biaya</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" id="kolomSkorRekomendasi" style="display:none;">Skor Rekomendasi
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailLombaPublik" tabindex="-1" aria-labelledby="modalDetailLombaPublikLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>


<script>
    var dtLomba;
    var isRekomendasiActive = false;
    // Ambil daftar kriteria dan urutan default dari Blade/PHP
    const kriteriaList = @json($kriteriaList ?? []);
    const defaultUrutanKriteriaKeys = @json($defaultUrutanKriteria ?? array_keys($kriteriaList));
    let sortableSlots = []; // Array untuk menyimpan instance SortableJS

    // Fungsi modal (jika belum global)
    if (typeof modalActionLomba === 'undefined') {
        function modalActionLomba(url, title = 'Detail Lomba', modalId = 'modalDetailLombaPublik') {
            const targetModal = $(`#${modalId}`);
            const targetModalContent = targetModal.find('.modal-content');
            targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat...</p></div>');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
            modalInstance.show();
            $.ajax({
                url: url, type: 'GET',
                success: function (response) { targetModalContent.html(response); },
                error: function (xhr) {
                    let msg = xhr.responseJSON?.message ?? 'Gagal memuat konten.';
                    targetModalContent.html(`<div class="modal-header bg-danger text-white"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${msg}</p></div>`);
                }
            });
        }
    }

    // Fungsi untuk menginisialisasi/mereset item kriteria di bank dan slot
    function setupKriteriaDraggable() {
        // Kosongkan bank dan slot sebelumnya
        $('#kriteriaBank').empty();
        $('.kriteria-dropzone').empty();

        // Hancurkan instance SortableJS sebelumnya jika ada
        sortableSlots.forEach(s => s.destroy());
        sortableSlots = [];

        // Isi bank dengan kriteria default
        defaultUrutanKriteriaKeys.forEach(key => {
            if (kriteriaList[key]) {
                $('#kriteriaBank').append(
                    `<div class="kriteria-item" data-kriteria-key="${key}">
                        <i class="fas fa-grip-vertical handle me-2"></i>${kriteriaList[key]}
                     </div>`
                );
            }
        });

        // Inisialisasi SortableJS untuk bank kriteria
        new Sortable(document.getElementById('kriteriaBank'), {
            group: 'sharedKriteria', // Nama grup yang sama untuk drag antar list
            animation: 150,
            sort: true // Memungkinkan pengurutan di dalam bank juga
        });

        // Inisialisasi SortableJS untuk setiap slot prioritas
        $('.kriteria-dropzone').each(function (index) {
            let slot = this;
            let sortable = new Sortable(slot, {
                group: 'sharedKriteria',
                animation: 150,
                onAdd: function (evt) {
                    // Hanya izinkan satu item per slot
                    if (slot.children.length > 1) {
                        // Pindahkan item yang berlebih kembali ke bank atau ke slot asal
                        let itemToMoveBack = (evt.item === slot.children[0]) ? slot.children[1] : slot.children[0];
                        document.getElementById('kriteriaBank').appendChild(itemToMoveBack);
                        // Beri feedback ke user bahwa slot hanya bisa diisi 1 item
                        if (!$(slot).find('.slot-warning').length) {
                            $(slot).append('<small class="text-danger slot-warning d-block mt-1">Slot hanya bisa diisi 1 kriteria.</small>');
                        }
                        setTimeout(() => $(slot).find('.slot-warning').remove(), 2000);
                    } else {
                        $(slot).find('.slot-warning').remove();
                    }
                },
                onRemove: function (evt) {
                    $(slot).find('.slot-warning').remove();
                }
            });
            sortableSlots.push(sortable);
        });
    }


    $(document).ready(function () {
        setupKriteriaDraggable(); // Setup awal

        $('#resetUrutanBtn').on('click', function () {
            setupKriteriaDraggable(); // Reset ke kondisi awal
            if (isRekomendasiActive) {
                isRekomendasiActive = false;
                dtLomba.column('#kolomSkorRekomendasi').visible(false);
                $('#infoRekomendasi').addClass('d-none');
                dtLomba.ajax.reload();
                Swal.fire({ icon: 'info', title: 'Mode Rekomendasi Dinonaktifkan', text: 'Urutan prioritas telah direset.', timer: 2000, showConfirmButton: false });
            }
        });

        dtLomba = $('#dataTableLomba').DataTable({
            // ... (konfigurasi DataTables sama seperti sebelumnya) ...
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('lomba.getList') }}",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function (d) {
                    d.search_nama = $('#search_nama').val();
                    d.filter_status = $('#filter_status').val();
                    d.rekomendasi = isRekomendasiActive ? '1' : '0';
                    if (isRekomendasiActive) {
                        let urutanKriteriaArray = [];
                        $('.kriteria-dropzone').each(function () {
                            let item = $(this).find('.kriteria-item');
                            if (item.length) {
                                urutanKriteriaArray.push(item.data('kriteria-key'));
                            } else {
                                urutanKriteriaArray.push(null); // atau placeholder jika slot kosong
                            }
                        });
                        d.urutan_kriteria = urutanKriteriaArray;
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error("DataTables AJAX error: ", xhr.responseText);
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Gagal memuat data lomba. Silakan coba lagi atau hubungi admin.' });
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'lomba.nama_lomba' },
                { data: 'penyelenggara', name: 'lomba.penyelenggara' },
                { data: 'bidang_display', name: 'bidang_display', orderable: false, searchable: false },
                { data: 'tingkat', name: 'lomba.tingkat', className: 'text-center' },
                { data: 'batas_pendaftaran', name: 'lomba.batas_pendaftaran', className: 'text-center' },
                { data: 'biaya_display', name: 'lomba.biaya', className: 'text-center', orderable: true },
                { data: 'status_display', name: 'status_display', className: 'text-center', orderable: false, searchable: false },
                { data: 'moora_score', name: 'moora_score', className: 'text-center', visible: false, orderable: true },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            order: isRekomendasiActive ? [[8, 'desc']] : [[5, 'asc']], // Kolom skor adalah index ke-8 (0-based), atau batas daftar
            fnDrawCallback: function (oSettings) {
                if (isRekomendasiActive && oSettings.fnRecordsDisplay() == 0 && oSettings.aiDisplay.length === 0 && !oSettings.oAjaxData.sSearch) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Rekomendasi',
                        text: 'Tidak ditemukan lomba yang sesuai dengan prioritas kriteria Anda saat ini.',
                        showConfirmButton: true // Biarkan user menutupnya
                    });
                }
            }
        });

        $('#terapkanUrutanBtn').on('click', function () {
            let kriteriaDiSlot = 0;
            $('.kriteria-dropzone .kriteria-item').each(function () {
                kriteriaDiSlot++;
            });

            if (kriteriaDiSlot < defaultUrutanKriteriaKeys.length) { // Cek apakah semua slot terisi
                Swal.fire('Peringatan', `Harap isi semua ${defaultUrutanKriteriaKeys.length} slot prioritas dengan kriteria dari bank.`, 'warning');
                return;
            }

            isRekomendasiActive = true;
            dtLomba.column('#kolomSkorRekomendasi').visible(true);
            dtLomba.order([dtLomba.column(':contains(Skor Rekomendasi)').index(), 'desc']).ajax.reload(function (json) {
                if (json && json.recordsFiltered > 0) {
                    Swal.fire({ icon: 'success', title: 'Rekomendasi Dimuat!', text: 'Hasil rekomendasi berdasarkan prioritas Anda ditampilkan.', timer: 2000, showConfirmButton: false });
                }
            }, false);
            $('#infoRekomendasi').removeClass('d-none');
            $('#filterFormLomba').trigger('reset');
            $('#search_nama').val('');
            $('#filter_status').val('');
        });

        $('#filterFormLomba').on('submit', function (e) {
            e.preventDefault();
            isRekomendasiActive = false;
            dtLomba.column('#kolomSkorRekomendasi').visible(false);
            dtLomba.order([dtLomba.column(':contains(Batas Daftar)').index(), 'asc']).ajax.reload();
            $('#infoRekomendasi').addClass('d-none');
        });
    });
</script>
@endpush --}}

{{-- Slider --}}
{{-- @extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Informasi & Rekomendasi Lomba')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .form-label-group {
        margin-bottom: 0.5rem;
    }

    .slider-container {
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .weight-value {
        font-weight: bold;
        min-width: 30px;
        /* Agar tampilan tidak meloncat saat angka berubah */
        display: inline-block;
        text-align: right;
    }

    #totalBobotText {
        font-weight: bold;
    }

    .bobot-warning {
        color: #dc3545;
        /* Bootstrap danger color */
        font-weight: bold;
    }

    .card-title-small {
        font-size: 1rem;
        font-weight: 500;
    }
</style>
@endpush --}}

{{-- @section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $breadcrumb->title ?? 'Daftar Lomba' }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-end">
                        <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseBobotKriteria" aria-expanded="false"
                            aria-controls="collapseBobotKriteria">
                            <i class="fas fa-cogs me-1"></i> Atur Prioritas Rekomendasi Lomba
                        </button>
                    </div>

                    <div class="collapse mb-4" id="collapseBobotKriteria">
                        <div class="card card-body border-info">
                            <h5 class="card-title card-title-small">Sesuaikan Prioritas Kriteria Rekomendasi</h5>
                            <p class="card-text small text-muted">Geser slider untuk menentukan seberapa penting setiap
                                kriteria. Total bobot harus 100.</p>
                            <form id="formBobotKriteria" class="mt-2">
                                @foreach($kriteriaUntukBobot as $key => $label)
                                <div class="form-label-group row align-items-center mb-1">
                                    <label for="bobot_{{ $key }}"
                                        class="col-sm-4 col-form-label col-form-label-sm pe-0">{{ $label }}:</label>
                                    <div class="col-sm-6 slider-container">
                                        <input type="range" class="form-range bobot-slider" id="bobot_{{ $key }}"
                                            data-kriteria="{{ $key }}" min="0" max="50"
                                            value="{{ $defaultBobotView[$key] ?? 20 }}" step="5">
                                    </div>
                                    <div class="col-sm-2 ps-1">
                                        <span class="weight-value" id="value_{{ $key }}">{{ $defaultBobotView[$key] ??
                                            20 }}</span>
                                    </div>
                                </div>
                                @endforeach
                                <div class="row mt-3">
                                    <div class="col-sm-4"><strong>Total Bobot:</strong></div>
                                    <div class="col-sm-8"><strong id="totalBobotText">100</strong> <span
                                            id="bobotWarningText" class="small bobot-warning"></span></div>
                                </div>
                                <div class="mt-3">
                                    <button type="button" id="resetBobotBtn"
                                        class="btn btn-sm btn-outline-secondary me-2"><i
                                            class="fas fa-undo me-1"></i>Reset Bobot</button>
                                    <button type="button" id="terapkanBobotBtn" class="btn btn-sm btn-success">
                                        <i class="fas fa-check me-1"></i> Terapkan & Lihat Rekomendasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <form id="filterFormLomba" class="row gx-3 gy-2 align-items-center mb-4">
                        <div class="col-sm-12 col-md-5">
                            <label class="form-label visually-hidden" for="search_nama">Cari Nama Lomba</label>
                            <input type="text" class="form-control form-control-sm" id="search_nama"
                                placeholder="Cari nama lomba...">
                        </div>
                        <div class="col-sm-12 col-md-4">
                            <label class="form-label visually-hidden" for="filter_status">Status Pendaftaran</label>
                            <select class="form-select form-select-sm" id="filter_status">
                                <option value="">Semua Status Pendaftaran</option>
                                <option value="buka">Buka</option>
                                <option value="tutup">Tutup</option>
                                <option value="segera hadir">Segera Hadir</option>
                            </select>
                        </div>
                        <div class="col-sm-12 col-md-3">
                            <button type="submit" class="btn btn-sm btn-primary w-100"><i
                                    class="fas fa-search me-1"></i> Cari Lomba</button>
                        </div>
                    </form>
                    <div id="infoRekomendasi" class="alert alert-info alert-dismissible fade show d-none" role="alert">
                        <i class="fas fa-info-circle me-2"></i>Menampilkan rekomendasi lomba berdasarkan preferensi
                        Anda. Untuk pencarian biasa, gunakan filter di atas dan klik "Cari Lomba".
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <table class="table table-bordered table-hover dt-responsive nowrap" id="dataTableLomba"
                        style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center">No.</th>
                                <th>Nama Lomba</th>
                                <th>Penyelenggara</th>
                                <th>Bidang</th>
                                <th>Tingkat</th>
                                <th>Batas Daftar</th>
                                <th class="text-center">Biaya</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" id="kolomSkorRekomendasi" style="display:none;">Skor Rekomendasi
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailLombaPublik" tabindex="-1" aria-labelledby="modalDetailLombaPublikLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    var dtLomba;
    var isRekomendasiActive = false;
    // Simpan bobot default dari Blade untuk reset
    const defaultBobotView = @json($defaultBobotView ?? []);

    if (typeof modalActionLomba === 'undefined') {
        function modalActionLomba(url, title = 'Detail Lomba', modalId = 'modalDetailLombaPublik') {
            const targetModal = $(`#${modalId}`);
            const targetModalContent = targetModal.find('.modal-content');
            targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat...</p></div>');
            const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
            modalInstance.show();
            $.ajax({
                url: url, type: 'GET',
                success: function (response) { targetModalContent.html(response); },
                error: function (xhr) {
                    let msg = xhr.responseJSON?.message ?? 'Gagal memuat konten.';
                    targetModalContent.html(`<div class="modal-header bg-danger text-white"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${msg}</p></div>`);
                }
            });
        }
    }

    function updateTotalBobotDisplay() {
        let total = 0;
        $('.bobot-slider').each(function () {
            total += parseInt($(this).val());
        });
        $('#totalBobotText').text(total); // Hanya angka, tanpa %
        if (total !== 100) {
            $('#totalBobotText').addClass('bobot-warning');
            $('#bobotWarningText').text('(Total harus 100)');
            $('#terapkanBobotBtn').prop('disabled', true);
        } else {
            $('#totalBobotText').removeClass('bobot-warning');
            $('#bobotWarningText').text('');
            $('#terapkanBobotBtn').prop('disabled', false);
        }
    }

    $(document).ready(function () {
        $('.bobot-slider').on('input', function () {
            $('#value_' + $(this).data('kriteria')).text($(this).val());
            updateTotalBobotDisplay();
        });
        updateTotalBobotDisplay();

        $('#resetBobotBtn').on('click', function () {
            $('.bobot-slider').each(function () {
                const kriteriaKey = $(this).data('kriteria');
                const defaultValue = defaultBobotView[kriteriaKey] !== undefined ? defaultBobotView[kriteriaKey] : 20;
                $(this).val(defaultValue);
                $('#value_' + kriteriaKey).text(defaultValue);
            });
            updateTotalBobotDisplay();
            if (isRekomendasiActive) {
                isRekomendasiActive = false;
                dtLomba.column('#kolomSkorRekomendasi').visible(false);
                $('#infoRekomendasi').addClass('d-none');
                dtLomba.ajax.reload();
                Swal.fire({ icon: 'info', title: 'Mode Rekomendasi Dinonaktifkan', text: 'Menampilkan semua lomba.', timer: 2000, showConfirmButton: false });
            }
        });

        dtLomba = $('#dataTableLomba').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('lomba.getList') }}",
                type: "GET",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: function (d) {
                    d.search_nama = $('#search_nama').val();
                    d.filter_status = $('#filter_status').val();
                    d.rekomendasi = isRekomendasiActive ? '1' : '0';
                    if (isRekomendasiActive) {
                        d.weights = {};
                        $('.bobot-slider').each(function () {
                            // Kirim bobot sebagai desimal (nilai slider / 100)
                            d.weights[$(this).data('kriteria')] = parseInt($(this).val()) / 100;
                        });
                    }
                },
                error: function (xhr, error, thrown) {
                    console.error("DataTables AJAX error: ", xhr.responseText);
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Gagal memuat data lomba. Silakan coba lagi atau hubungi admin.' });
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'lomba.nama_lomba' }, // tambahkan prefix tabel jika join
                { data: 'penyelenggara', name: 'lomba.penyelenggara' },
                { data: 'bidang_display', name: 'bidang_display', orderable: false, searchable: false },
                { data: 'tingkat', name: 'lomba.tingkat', className: 'text-center' },
                // { data: 'pembukaan_pendaftaran', name: 'lomba.pembukaan_pendaftaran', className: 'text-center' }, // Jika ingin sort by tanggal buka
                { data: 'batas_pendaftaran', name: 'lomba.batas_pendaftaran', className: 'text-center' },
                { data: 'biaya_display', name: 'lomba.biaya', className: 'text-center' }, // Sort by biaya asli
                { data: 'status_display', name: 'status_display', className: 'text-center', orderable: false, searchable: false },
                { data: 'moora_score', name: 'moora_score', className: 'text-center', visible: false, orderable: true }, // Default orderable, akan diorder jika kolom visible
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            order: isRekomendasiActive ? [[9, 'desc']] : [[5, 'asc']], // Order by score jika rekomendasi, else by batas_pendaftaran
            createdRow: function (row, data, dataIndex) {
                if (isRekomendasiActive) {
                    $(row).find('td:eq(9)').show(); // Tampilkan kolom skor jika mode rekomendasi
                } else {
                    $(row).find('td:eq(9)').hide(); // Sembunyikan jika tidak
                }
            },
            fnDrawCallback: function (oSettings) {
                if (isRekomendasiActive && oSettings.fnRecordsDisplay() > 0) {
                    // No need for Swal here if table loads, it might be annoying on every sort/page change
                } else if (isRekomendasiActive && oSettings.fnRecordsDisplay() == 0 && oSettings.aiDisplay.length === 0 && !oSettings.oAjaxData.sSearch) { // Hanya tampilkan jika tidak ada hasil & bukan karena search global
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Rekomendasi',
                        text: 'Tidak ditemukan lomba yang sesuai dengan preferensi bobot Anda saat ini.',
                        timer: 3000,
                        showConfirmButton: true
                    });
                }
            }
        });

        $('#terapkanBobotBtn').on('click', function () {
            if (parseInt($('#totalBobotText').text()) !== 100) {
                Swal.fire('Peringatan', 'Total bobot kriteria harus 100 untuk menerapkan rekomendasi.', 'warning');
                return;
            }
            isRekomendasiActive = true;
            dtLomba.column('#kolomSkorRekomendasi').visible(true);
            dtLomba.order([dtLomba.column(':contains(Skor Rekomendasi)').index(), 'desc']).draw(); // Order by skor desc
            $('#infoRekomendasi').removeClass('d-none');
            $('#filterFormLomba').trigger('reset'); // Reset filter pencarian biasa agar tidak bentrok
            $('#search_nama').val(''); // Pastikan search box juga direset
            $('#filter_status').val('');
            // dtLomba.ajax.reload(); // Sudah dipanggil oleh draw() di atas
            Swal.fire({ icon: 'info', title: 'Mode Rekomendasi Aktif', text: 'Mencari lomba berdasarkan prioritas Anda...', timer: 1500, showConfirmButton: false });

        });

        $('#filterFormLomba').on('submit', function (e) {
            e.preventDefault();
            isRekomendasiActive = false;
            dtLomba.column('#kolomSkorRekomendasi').visible(false);
            dtLomba.order([dtLomba.column(':contains(Batas Daftar)').index(), 'asc']).draw(); // Kembalikan order default
            $('#infoRekomendasi').addClass('d-none');
            // dtLomba.ajax.reload(); // Sudah dipanggil oleh draw()
        });
    });
</script>
@endpush --}}


















{{-- @extends('layouts.template')
@section('title', 'Daftar Lomba')

@push('css') --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Tambahkan sedikit padding untuk tombol di dalam form filter agar terlihat lebih rapi */
        #filterFormLomba .btn {
            margin-top: 1.85rem;
            /* Sesuaikan jika label form tidak 'visually-hidden' */
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .form-label:not(.visually-hidden)+#filterFormLomba .btn {
            margin-top: 0;
            /* Atur kembali jika label tidak hidden */
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Daftar Lomba</h3>
                    </div>

                    <div class="card-body">
                        <form id="filterFormLomba" class="row g-3 mb-4 align-items-start">
                            <div class="col-md-3">
                                <label for="filter_status" class="form-label">Status Lomba</label>
                                <select class="form-select form-select-sm" id="filter_status" name="filter_status">
                                    <option value="">-- Semua Status --</option>
                                    <option value="Buka" {{ request('filter_status') == 'Buka' ? 'selected' : '' }}>Buka
                                    </option>
                                    <option value="Tutup" {{ request('filter_status') == 'Tutup' ? 'selected' : '' }}>Tutup
                                    </option>
                                    <option value="Segera Hadir" {{ request('filter_status') == 'Segera Hadir' ? 'selected' : '' }}>Segera Hadir</option>
                                </select>
                            </div>

                            <div class="col-md-auto">
                                <button type="submit" id="btnFilter" class="btn btn-primary btn-sm">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-auto">
                                <button type="button" id="btnReset" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </button>
                                <button type="button" id="btnRekomendasi" class="btn btn-success btn-sm">
                                    <i class="fas fa-star"></i> Tampilkan Rekomendasi Saya
                                </button>
                            </div>
                        </form>

                        <div id="infoRekomendasi" class="alert alert-info alert-dismissible fade show sma ll p-2"
                            role="alert" style="display: none;">
                            <strong><i class="fas fa-info-circle"></i> Info:</strong> Menampilkan daftar lomba berdasarkan
                            hasil rekomendasi MOORA untuk preferensi Anda.
                            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"
                                aria-label="Close"></button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped" id="dataDaftarLomba"
                                style="width:100%;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width:5%;">No.</th>
                                        <th style="width:25%;">Nama Lomba</th>
                                        <th style="width:15%;">Kategori/Bidang</th>
                                        <th style="width:15%;">Pembukaan</th>
                                        <th style="width:15%;">Penutupan</th>
                                        <th class="text-center" id="thSkorMoora" style="width:10%;">Skor</th>
                                        {{-- <th class="text-center" style="width:10%;">Status</th> --}}
                                        {{-- <th class="text-center" style="width:10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Memuat...</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center">Memuat konten...</p>
                </div>
            </div>
        </div>
    </div> --}}
{{-- @endsection

@push('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        var dataDaftarLomba;
        var isRekomendasiAktif = false; // Flag untuk status rekomendasi

        // Fungsi untuk memuat konten ke dalam modal via AJAX
        function modalAction(url, modalId = 'myModal', modalTitle = 'Aksi') {
            const modalElement = $(`#${modalId}`);
            const modalContent = modalElement.find('.modal-content'); // Target .modal-content untuk refresh
            const modalBody = modalElement.find('.modal-body');
            const titleElement = modalElement.find('.modal-title');

            titleElement.text('Memuat...');
            modalBody.html('<p class="text-center">Memuat konten...</p>'); // Reset body

            // Pastikan modal BS5 digunakan
            const bsModal = new bootstrap.Modal(document.getElementById(modalId));
            bsModal.show();

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {
                    // Ganti seluruh konten modal agar event listener pada form baru bisa ter-attach dengan benar
                    modalContent.html(response);
                    // Jika response tidak termasuk modal-header & modal-title, Anda mungkin perlu set manual
                    // Misalnya, jika response hanya bagian body:
                    // titleElement.text(modalTitle);
                    // modalBody.html(response);
                },
                error: function (xhr) {
                    titleElement.text('Error');
                    let errorMessage = 'Gagal memuat konten modal.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        // Jika response bukan JSON, tampilkan sebagai HTML (mungkin halaman error Laravel)
                        modalBody.html('<div class="alert alert-danger p-2">Terjadi kesalahan. Silakan coba lagi.</div><div class="mt-2 p-2 border bg-light" style="max-height: 300px; overflow-y: auto;"><code>' + xhr.responseText + '</code></div>');
                        return;
                    }
                    modalBody.html('<div class="alert alert-danger">' + errorMessage + '</div>');
                    Swal.fire({ icon: 'error', title: 'Error Memuat Modal', text: errorMessage });
                }
            });
        }

        $(document).ready(function () {
            // Inisialisasi DataTables
            dataDaftarLomba = $('#dataDaftarLomba').DataTable({
                processing: true,
                serverSide: true,
                responsive: true, // Membuat tabel responsif
                ajax: {
                    url: "{{ route('lomba.getList') }}", // Pastikan route ini benar
                    data: function (d) {
                        // Kirim data filter dan flag rekomendasi ke server
                        d.search_nama = $('#search_nama').val();
                        d.filter_status = $('#filter_status').val();
                        d.rekomendasi = isRekomendasiAktif ? 1 : 0;
                    },
                    // Menambahkan error handling untuk AJAX DataTables
                    error: function (xhr, error, thrown) {
                        console.error("DataTables AJAX error: ", xhr, error, thrown);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memuat Data',
                            text: 'Tidak dapat mengambil data lomba dari server. Silakan coba lagi nanti. Error: ' + xhr.status + ' ' + thrown,
                        });
                        // Hentikan indikator processing DataTables
                        $('#dataDaftarLomba_processing').hide();
                    }
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                    { data: 'nama_lomba', name: 'nama_lomba' },
                    { data: 'kategori', name: 'kategori' },
                    { data: 'pembukaan_pendaftaran', name: 'pembukaan_pendaftaran' },
                    { data: 'batas_pendaftaran', name: 'batas_pendaftaran' },
                    { data: 'moora_score', name: 'moora_score', className: 'text-center' },
                    // { data: 'status', name: 'status', className: 'text-center' }, // Diubah dari status_verifikasi
                    { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json", // Bahasa Indonesia
                    processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Memuat...</span></div> Mohon tunggu...'
                },
                order: [], // Tidak ada pengurutan default di client-side, biarkan server yang menentukan
                // Callback setelah DataTables selesai menggambar tabel
                drawCallback: function (settings) {
                    if (isRekomendasiAktif) {
                        $('#infoRekomendasi').slideDown();
                        $('#thSkorMoora').text('Skor MOORA'); // Ubah header kolom skor
                    } else {
                        $('#infoRekomendasi').slideUp();
                        $('#thSkorMoora').text('Skor'); // Kembalikan header default
                    }
                    // Inisialisasi tooltip Bootstrap jika ada tombol dengan data-bs-toggle="tooltip"
                    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });
                }
            });

            // Event handler untuk tombol Filter (submit form)
            $('#filterFormLomba').on('submit', function (e) {
                e.preventDefault();
                isRekomendasiAktif = false; // Nonaktifkan rekomendasi saat filter manual
                dataDaftarLomba.ajax.reload(); // Muat ulang DataTables
            });

            // Event handler untuk tombol Reset Filter
            $('#btnReset').on('click', function () {
                $('#filterFormLomba')[0].reset(); // Reset isi form filter
                isRekomendasiAktif = false; // Nonaktifkan rekomendasi
                $('#search_nama').val(''); // Pastikan input search juga bersih
                $('#filter_status').val(''); // Pastikan select status juga bersih
                dataDaftarLomba.ajax.reload(); // Muat ulang DataTables
            });

            // Event handler untuk tombol Rekomendasi
            $('#btnRekomendasi').on('click', function () {
                isRekomendasiAktif = true; // Aktifkan flag rekomendasi
                $('#filterFormLomba')[0].reset(); // Reset filter lain agar tidak bentrok
                $('#search_nama').val('');
                $('#filter_status').val('');

                Swal.fire({
                    title: 'Mengambil Rekomendasi',
                    text: 'Sedang memproses preferensi Anda, mohon tunggu...',
                    imageUrl: 'https://i.gifer.com/ZZ5H.gif', // Contoh loading GIF
                    imageWidth: 100,
                    imageHeight: 100,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        // Swal.showLoading() // Bisa juga pakai ini jika tidak mau custom GIF
                    }
                });

                // Muat ulang DataTables dengan flag rekomendasi
                dataDaftarLomba.ajax.reload(function (json) { // Callback setelah reload selesai
                    Swal.close(); // Tutup loading SweetAlert
                    if (json && json.recordsFiltered > 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Rekomendasi Dimuat!',
                            text: 'Hasil rekomendasi MOORA telah berhasil ditampilkan.',
                            timer: 2500,
                            showConfirmButton: false
                        });
                    } else if (json && json.recordsFiltered === 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Tidak Ada Rekomendasi',
                            text: 'Saat ini belum ada rekomendasi lomba yang sesuai dengan preferensi Anda.',
                        });
                    } else if (!json) { // Jika json tidak ada (kemungkinan error AJAX)
                        // Error sudah ditangani oleh DataTables AJAX error handler,
                        // tapi bisa juga ditambahkan notifikasi spesifik di sini jika perlu.
                        console.warn("Callback reload DataTables menerima JSON null atau undefined.");
                    }
                }, false); // 'false' agar paging tidak direset, meskipun untuk rekomendasi mungkin lebih baik direset
            });

            // Menutup alert info rekomendasi secara manual jika ada tombol close di dalamnya
            // Ini sudah ditangani Bootstrap otomatis jika menggunakan data-bs-dismiss="alert"
            // $('#infoRekomendasi .btn-close').on('click', function() {
            //     $('#infoRekomendasi').slideUp();
            // });

        });
    </script>
@endpush --}}