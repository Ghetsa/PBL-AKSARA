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
        $('.bobot-slider').each(function() {
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

    $(document).ready(function() {
        $('.bobot-slider').on('input', function() {
            $('#value_' + $(this).data('kriteria')).text($(this).val());
            updateTotalBobotDisplay();
        });
        updateTotalBobotDisplay();

        $('#resetBobotBtn').on('click', function() {
            $('.bobot-slider').each(function() {
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
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: function (d) {
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
                { data: 'status_display', name: 'status_display', className: 'text-center', orderable: false, searchable: false},
                { data: 'moora_score', name: 'moora_score', className: 'text-center', visible: false, orderable: true }, // Default orderable, akan diorder jika kolom visible
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            order: isRekomendasiActive ? [[9, 'desc']] : [[5, 'asc']], // Order by score jika rekomendasi, else by batas_pendaftaran
            createdRow: function(row, data, dataIndex) {
                if (isRekomendasiActive) {
                    $(row).find('td:eq(9)').show(); // Tampilkan kolom skor jika mode rekomendasi
                } else {
                    $(row).find('td:eq(9)').hide(); // Sembunyikan jika tidak
                }
            },
             fnDrawCallback: function (oSettings) {
                if (isRekomendasiActive && oSettings.fnRecordsDisplay() > 0) {
                    // No need for Swal here if table loads, it might be annoying on every sort/page change
                } else if (isRekomendasiActive && oSettings.fnRecordsDisplay() == 0 && oSettings.aiDisplay.length === 0 && !oSettings.oAjaxData.sSearch ) { // Hanya tampilkan jika tidak ada hasil & bukan karena search global
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
            dtLomba.column('#kolomSkorRekomendasi').visible(true);
            dtLomba.order([dtLomba.column(':contains(Skor Rekomendasi)').index(), 'desc']).draw(); // Order by skor desc
            $('#infoRekomendasi').removeClass('d-none');
            $('#filterFormLomba').trigger('reset'); // Reset filter pencarian biasa agar tidak bentrok
            $('#search_nama').val(''); // Pastikan search box juga direset
            $('#filter_status').val('');
            // dtLomba.ajax.reload(); // Sudah dipanggil oleh draw() di atas
            Swal.fire({ icon: 'info', title: 'Mode Rekomendasi Aktif', text: 'Mencari lomba berdasarkan prioritas Anda...', timer: 1500, showConfirmButton: false });

        });

        $('#filterFormLomba').on('submit', function(e) {
            e.preventDefault();
            isRekomendasiActive = false;
            dtLomba.column('#kolomSkorRekomendasi').visible(false);
            dtLomba.order([dtLomba.column(':contains(Batas Daftar)').index(), 'asc']).draw(); // Kembalikan order default
            $('#infoRekomendasi').addClass('d-none');
            // dtLomba.ajax.reload(); // Sudah dipanggil oleh draw()
        });
    });
</script>
@endpush

{{-- @extends('layouts.template') 
@section('title', 'Daftar Lomba')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /* Tambahkan sedikit padding untuk tombol di dalam form filter agar terlihat lebih rapi */
    #filterFormLomba .btn {
        margin-top: 1.85rem; /* Sesuaikan jika label form tidak 'visually-hidden' */
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
    .form-label:not(.visually-hidden) + #filterFormLomba .btn {
        margin-top: 0; /* Atur kembali jika label tidak hidden */
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
                                <option value="Buka" {{ request('filter_status') == 'Buka' ? 'selected' : '' }}>Buka</option>
                                <option value="Tutup" {{ request('filter_status') == 'Tutup' ? 'selected' : '' }}>Tutup</option>
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
                        </div>

                             <button type="button" id="btnRekomendasi" class="btn btn-success btn-sm">
                                <i class="fas fa-star"></i> Tampilkan Rekomendasi Saya
                            </button>
                        </div>
                    </form>

                    <div id="infoRekomendasi" class="alert alert-info alert-dismissible fade show small p-2" role="alert" style="display: none;">
                        <strong><i class="fas fa-info-circle"></i> Info:</strong> Menampilkan daftar lomba berdasarkan hasil rekomendasi MOORA untuk preferensi Anda.
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="dataDaftarLomba" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width:5%;">No.</th>
                                    <th style="width:25%;">Nama Lomba</th>
                                    <th style="width:15%;">Kategori/Bidang</th>
                                    <th style="width:15%;">Pembukaan</th>
                                    <th style="width:15%;">Penutupan</th>
                                    <th class="text-center" id="thSkorMoora" style="width:10%;">Skor</th> 
                                    <th class="text-center" style="width:10%;">Status</th>
                                    <th class="text-center" style="width:10%;">Aksi</th>
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
</div>
@endsection

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
            success: function(response) {
                // Ganti seluruh konten modal agar event listener pada form baru bisa ter-attach dengan benar
                modalContent.html(response);
                // Jika response tidak termasuk modal-header & modal-title, Anda mungkin perlu set manual
                // Misalnya, jika response hanya bagian body:
                // titleElement.text(modalTitle);
                // modalBody.html(response);
            },
            error: function(xhr) {
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

    $(document).ready(function() {
        // Inisialisasi DataTables
        dataDaftarLomba = $('#dataDaftarLomba').DataTable({
            processing: true,
            serverSide: true,
            responsive: true, // Membuat tabel responsif
            ajax: {
                url: "{{ route('lomba.getList') }}", // Pastikan route ini benar
                data: function(d) {
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
                { data: 'status', name: 'status', className: 'text-center' }, // Diubah dari status_verifikasi
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json", // Bahasa Indonesia
                 processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Memuat...</span></div> Mohon tunggu...'
            },
            order: [], // Tidak ada pengurutan default di client-side, biarkan server yang menentukan
            // Callback setelah DataTables selesai menggambar tabel
            drawCallback: function( settings ) {
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
        $('#filterFormLomba').on('submit', function(e) {
            e.preventDefault();
            isRekomendasiAktif = false; // Nonaktifkan rekomendasi saat filter manual
            dataDaftarLomba.ajax.reload(); // Muat ulang DataTables
        });

        // Event handler untuk tombol Reset Filter
        $('#btnReset').on('click', function() {
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