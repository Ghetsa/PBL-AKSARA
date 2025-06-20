@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Informasi & Rekomendasi Lomba')

@push('css')
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"> --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> --}}
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
                {{-- <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ $breadcrumb->title ?? 'Daftar Lomba' }}</h6>
                </div> --}}
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h4 class="card-title mb-0">Rekomendasi Lomba Untuk Anda</h4>
                    {{-- <div class="card-tools d-flex flex-wrap gap-1 mt-2 mt-md-0">
                        <button class="btn btn-sm btn-outline-primary" 
                                onclick="showMooraDetails('{{ route('lomba.mhs.details.all') }}')">
                            <i class="fas fa-calculator me-1"></i> Lihat Detail Perhitungan
                        </button>
                    </div> --}}
                </div>
                <div class="card-body">
                    {{-- Tombol Toggle untuk Form Bobot Kriteria --}}
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="card-tools d-flex flex-wrap gap-1 mt-2 mt-md-0">
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="showMooraDetails('{{ route('lomba.mhs.details.all') }}')">
                                <i class="fas fa-calculator me-1"></i> Lihat Detail Perhitungan
                            </button>
                        </div>
                        <div class="card-tools d-flex flex-wrap gap-1 mt-2 mt-md-0">
                            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBobotKriteria" aria-expanded="false" aria-controls="collapseBobotKriteria">
                                <i class="fas fa-cogs me-1"></i> Atur Prioritas Rekomendasi Lomba
                            </button>
                        </div>
                        {{-- <button class="btn btn-sm btn-outline-primary" onclick="showMooraDetails('{{ route('lomba.mhs.details.all') }}')">
                            <i class="fas fa-calculator me-1"></i> Lihat Detail Perhitungan
                        </button> --}}

                    </div>

                    {{-- Form Bobot Kriteria (Collapsible) --}}
                    <div class="collapse mb-4 mt-3" id="collapseBobotKriteria">
                        <div class="card card-body border-info">
                            <h5 class="card-title card-title-small">Sesuaikan Prioritas Kriteria Rekomendasi</h5>
                            <p class="card-text small text-muted">Geser slider untuk menentukan seberapa penting setiap kriteria. Total bobot harus 100.</p>
                            <form id="formBobotKriteria" class="mt-2">
                                @foreach($kriteriaUntukBobot as $key => $label)
                                <div class="form-label-group row align-items-center mb-1">
                                    <label for="bobot_{{ $key }}" class="col-sm-4 col-form-label col-form-label-sm pe-0">{{ $label }}:</label>
                                    <div class="col-sm-6 slider-container">
                                        <input type="range" class="form-range bobot-slider" id="bobot_{{ $key }}" data-kriteria="{{ $key }}" min="0" max="50" value="{{ $bobotView[$key] ?? 20 }}" step="5">
                                    </div>
                                    <div class="col-sm-2 ps-1">
                                        <span class="weight-value" id="value_{{ $key }}">{{ $bobotView[$key] ?? 20 }}</span>
                                    </div>
                                </div>
                                @endforeach
                                <div class="row mt-3">
                                    <div class="col-sm-4"><strong>Total Bobot:</strong></div>
                                    <div class="col-sm-8"><strong id="totalBobotText">100</strong> <span id="bobotWarningText" class="small bobot-warning"></span></div>
                                </div>
                                <div class="d-flex justify-content-end align-items-center flex-wrap">
                                    <div class="card-tools d-flex flex-wrap gap-1 mt-3 mt-md-0">
                                           <button type="button" id="resetBobotBtn" class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-undo me-1"></i>Reset Bobot</button>
                                    </div>
                                    <div class="card-tools d-flex flex-wrap gap-1 mt-3 mt-md-0">
                                        <button type="button" id="terapkanBobotBtn" class="btn btn-sm btn-success">
                                            <i class="fas fa-check me-1"></i> Terapkan & Lihat Rekomendasi
                                        </button>
                                    </div>
                                </div>
                                {{-- <div class="mt-3">
                                    <button type="button" id="resetBobotBtn" class="btn btn-sm btn-outline-secondary me-2"><i class="fas fa-undo me-1"></i>Reset Bobot</button>
                                    <button type="button" id="terapkanBobotBtn" class="btn btn-sm btn-success">
                                        <i class="fas fa-check me-1"></i> Terapkan & Lihat Rekomendasi
                                    </button>
                                </div> --}}
                            </form>
                        </div>
                    </div>
                    
                    <div class="row mb-3 mt-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="filter_status" class="form-label small">Filter Status Pendaftaran:</label>
                                <select class="form-select form-select-sm" id="filter_status">
                                    <option value="">Semua Status Pendaftaran</option>
                                    <option value="buka">Buka</option>
                                    <option value="tutup">Tutup</option>
                                    <option value="segera hadir">Segera Hadir</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tingkat_lomba_filter_verifikasi" class="form-label small">Filter Tingkat Lomba:</label>
                                <select class="form-select form-select-sm" id="tingkat_lomba_filter_verifikasi">
                                    <option value="">- Semua Tingkat -</option>
                                    <option value="lokal">Lokal</option>
                                    <option value="nasional">Nasional</option>
                                    <option value="internasional">Internasional</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kategori_lomba_filter_verifikasi" class="form-label small">Filter Kategori Lomba:</label>
                                <select class="form-select form-select-sm" id="kategori_lomba_filter_verifikasi">
                                    <option value="">- Semua Kategori -</option>
                                    <option value="individu">Individu</option>
                                    <option value="kelompok">Kelompok</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- <form id="filterFormLomba" class="row gx-3 gy-2 align-items-center mb-4">
                        <div class="col-sm-12 col-md-4">
                            <label class="form-label visually-hidden" for="filter_status">Status Pendaftaran</label>
                            <select class="form-select form-select-sm" id="filter_status">
                                <option value="">Semua Status Pendaftaran</option>
                                <option value="buka">Buka</option>
                                <option value="tutup">Tutup</option>
                                <option value="segera hadir">Segera Hadir</option>
                            </select>
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
                        <div class="col-sm-12 col-md-4">
                            <label class="form-label visually-hidden" for="filter_status">Status Pendaftaran</label>
                            <select class="form-select form-select-sm" id="filter_status">
                                <option value="">Semua Status Pendaftaran</option>
                                <option value="buka">Buka</option>
                                <option value="tutup">Tutup</option>
                                <option value="segera hadir">Segera Hadir</option>
                            </select>
                        </div>
                    </form> --}}
                    {{-- <div id="infoRekomendasi" class="alert alert-info alert-dismissible fade show d-none" role="alert">
                        <i class="fas fa-info-circle me-2"></i>Menampilkan rekomendasi lomba berdasarkan preferensi Anda. Untuk pencarian biasa, gunakan filter di atas dan klik "Cari Lomba".
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div> --}}

                    <table class="table table-bordered table-hover dt-responsive wrap" id="dataTableLomba" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%">No.</th>
                                <th class="text-center">Nama Lomba</th>
                                <th class="text-center">Penyelenggara</th>
                                <th class="text-center">Bidang</th>
                                <th class="text-center">Tingkat</th>
                                <th class="text-center">Batas Daftar</th>
                                <th class="text-center">Biaya</th>
                                <th class="text-center">Status</th>
                                <th id="kolomSkorRekomendasi" style="display:none; width: 10%">Skor Rekomendasi</th>
                                <th>Aksi</th>
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
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>
<div class="modal fade" id="modalDetailHitungan" tabindex="-1" aria-labelledby="modalDetailHitunganLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" id="modalDetailHitunganContent"></div>
    </div>
</div>
{{-- [KONTAINER MODAL BARU] Tempat untuk memuat konten detail perhitungan --}}
<div class="modal fade" id="mooraDetailsModal" tabindex="-1" role="dialog" aria-labelledby="mooraDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document"> {{-- Menggunakan modal-xl untuk konten yang lebar --}}
        <div class="modal-content">
            {{-- Konten dari moora_details.blade.php akan dimuat di sini --}}
        </div>
    </div>
</div>
@endsection

@push('js')
{{-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}

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
                    // timer: 2000,
                    // showConfirmButton: false
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
                    d.tingkat_lomba_filter = $('#tingkat_lomba_filter_verifikasi').val(); // ID filter disesuaikan
                    d.kategori_lomba_filter = $('#kategori_lomba_filter_verifikasi').val(); // ID filter disesuaikan
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
                { data: 'nama_lomba', name: 'lomba.nama_lomba', orderable: false, searchable: true },
                { data: 'penyelenggara', name: 'lomba.penyelenggara', orderable: false, searchable: true },
                { data: 'bidang_display', name: 'bidang_display', orderable: false, searchable: true },
                { data: 'tingkat', name: 'lomba.tingkat', render: function(data, type, row) { return data.charAt(0).toUpperCase() + data.slice(1); }, orderable: false, searchable: true },
                { data: 'batas_pendaftaran', name: 'lomba.batas_pendaftaran', orderable: false, searchable: true },
                { data: 'biaya_display', name: 'lomba.biaya', className: 'text-nowrap', orderable: false, searchable: false },
                { data: 'status_display', name: 'status_display', orderable: false, searchable: false },
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
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            order: [[5, 'asc']], // Default order by batas_pendaftaran
            fnDrawCallback: function (oSettings) {
                if (isRekomendasiActive && oSettings.fnRecordsDisplay() == 0 && oSettings.aiDisplay.length === 0 && !oSettings.oAjaxData.sSearch) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Tidak Ada Rekomendasi',
                        text: 'Tidak ditemukan lomba yang sesuai dengan preferensi bobot Anda saat ini.',
                        // timer: 3000,
                        // showConfirmButton: true
                    });
                }
            }
        });

        $('#terapkanBobotBtn').on('click', function() {
            if (parseInt($('#totalBobotText').text()) !== 100) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Total bobot preferensi harus tepat 100%.',
                });
                return;
            }

            isRekomendasiActive = true;
            
            // [PERBAIKAN] Mengganti alert dengan SweetAlert
            Swal.fire({
                icon: 'info',
                title: 'Mode Rekomendasi Aktif',
                text: 'Menampilkan hasil yang diurutkan berdasarkan preferensi Anda.',
                timer: 2000,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Reload DataTable dengan callback
            dtLomba.ajax.reload(function(json) {
                // Cukup tutup loading Swal, tidak perlu menampilkan notifikasi sukses lagi
                // karena notifikasi info di atas sudah cukup sebagai konfirmasi.
                Swal.close(); 
            }, false);
            dtLomba.column('moora_score:name').visible(true); // Tampilkan kolom skor
            // if (parseInt($('#totalBobotText').text()) !== 100) {
            //     Swal.fire('Peringatan', 'Total bobot kriteria harus 100 untuk menerapkan rekomendasi.', 'warning');
            //     return;
            // }
            // isRekomendasiActive = true;
            // $('#infoRekomendasi').removeClass('d-none');
            // $('#filterFormLomba').trigger('reset'); // Reset form filter lainnya
            // $('#search_nama').val('');
            // $('#filter_status').val('');

            // // Ambil bobot yang diterapkan
            // const weights = {};
            // $('.bobot-slider').each(function() {
            //     weights[$(this).data('kriteria')] = parseInt($(this).val()) / 100;
            // });

            // // Kirim bobot sebagai bagian dari parameter AJAX
            // dtLomba.ajax.reload();
            // dtLomba.column('moora_score:name').visible(true); // Tampilkan kolom skor
            // dtLomba.order([dtLomba.column('moora_score:name').index(), 'desc']).draw(); // Order berdasarkan skor rekomendasi
        });

        $('#filterFormLomba').on('submit', function(e) {
            e.preventDefault();
            if (isRekomendasiActive) {
                isRekomendasiActive = false; // Nonaktifkan jika sedang mencari
                $('#infoRekomendasi').addClass('d-none');
                Swal.fire({
                    icon: 'info',
                    title: 'Mode Rekomendasi Dinonaktifkan',
                    // timer: 1500,
                    // showConfirmButton: false
                });
            }
            dtLomba.column('moora_score:name').visible(false); // Sembunyikan kolom skor saat filter biasa
            dtLomba.order([dtLomba.column('batas_pendaftaran:name').index(), 'asc']).draw(); // Kembalikan order default
        });

        $('#filter_status, #tingkat_lomba_filter_verifikasi, #kategori_lomba_filter_verifikasi').on('change', function () {
            dtLomba.ajax.reload();
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

    {{-- [SKRIP BARU] Skrip untuk memuat dan menampilkan modal detail MOORA --}}
    <script>
        function showMooraDetails(url) {
            const modal = $('#mooraDetailsModal');
            
            // Tampilkan loading spinner
            modal.find('.modal-content').html(`
                <div class="modal-body text-center p-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 mb-0">Memuat detail perhitungan...</p>
                </div>
            `);
            modal.modal('show');

            // Ambil bobot yang diterapkan dan kirimkan ke URL untuk menghitung perhitungan MOORA
            const weights = {};
            $('.bobot-slider').each(function() {
                weights[$(this).data('kriteria')] = parseInt($(this).val()) / 100;
            });

            const params = new URLSearchParams();
            for (const key in weights) {
                params.append(`weights[${key}]`, weights[key]);
            }

            $.get(url + `?${params.toString()}`, function(res) {
                modal.find('.modal-content').html(res);
                // [TAMBAHKAN INI] Panggil KaTeX untuk merender rumus di dalam modal
                // setelah kontennya berhasil dimuat.
                renderMathInElement(modal.find('.modal-content')[0], {
                    delimiters: [
                        {left: '$$', right: '$$', display: true},
                        {left: '\\[', right: '\\]', display: true},
                        {left: '\\(', right: '\\)', display: false}
                    ]
                });
            }).fail(function() {
                modal.find('.modal-content').html(`
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Terjadi Kesalahan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Gagal memuat detail perhitungan. Silakan coba lagi nanti.</p>
                    </div>
                `);
            });
        }
    </script>
@endpush
