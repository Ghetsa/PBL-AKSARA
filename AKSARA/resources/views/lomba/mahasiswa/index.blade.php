@extends('layouts.template') {{-- Sesuaikan dengan path layout utama Anda --}}
@section('title', 'Daftar Lomba')

{{-- Anda mungkin perlu menambahkan CSS tambahan untuk DataTables atau SweetAlert jika belum ada di layout utama --}}
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
{{-- Jika menggunakan FontAwesome --}}
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
                    {{-- Tombol Tambah Lomba --}}
                    {{-- Pertimbangkan untuk menambahkan @can('lomba-create') atau hak akses serupa --}}
                </div>

                <div class="card-body">
                    {{-- Form Filter --}}
                    <form id="filterFormLomba" class="row g-3 mb-4 align-items-start">
                        <div class="col-md-3">
                            <label for="filter_status" class="form-label">Status Lomba</label>
                            <select class="form-select form-select-sm" id="filter_status" name="filter_status">
                                <option value="">-- Semua Status --</option>
                                {{-- Sesuaikan value dan teks dengan status yang ada di database Anda --}}
                                <option value="Buka" {{ request('filter_status') == 'Buka' ? 'selected' : '' }}>Buka</option>
                                <option value="Tutup" {{ request('filter_status') == 'Tutup' ? 'selected' : '' }}>Tutup</option>
                                <option value="Segera Hadir" {{ request('filter_status') == 'Segera Hadir' ? 'selected' : '' }}>Segera Hadir</option>
                                {{-- Tambahkan opsi status lain jika ada --}}
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

                        <div class="col-md-auto ms-md-auto"> {{-- ms-md-auto untuk mendorong ke kanan pada layar medium ke atas --}}
                             <button type="button" id="btnRekomendasi" class="btn btn-success btn-sm">
                                <i class="fas fa-star"></i> Tampilkan Rekomendasi Saya
                            </button>
                        </div>
                    </form>

                    {{-- Pesan Info Rekomendasi (dikontrol oleh JavaScript) --}}
                    <div id="infoRekomendasi" class="alert alert-info alert-dismissible fade show small p-2" role="alert" style="display: none;">
                        <strong><i class="fas fa-info-circle"></i> Info:</strong> Menampilkan daftar lomba berdasarkan hasil rekomendasi MOORA untuk preferensi Anda.
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>

                    {{-- Tabel DataTables --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" id="dataDaftarLomba" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width:5%;">No.</th>
                                    <th style="width:25%;">Nama Lomba</th>
                                    <th style="width:15%;">Kategori/Bidang</th>
                                    <th style="width:15%;">Pembukaan</th>
                                    <th style="width:15%;">Penutupan</th>
                                    <th class="text-center" id="thSkorMoora" style="width:10%;">Skor</th> {{-- Header diubah via JS --}}
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

{{-- Modal untuk form AJAX (Tambah/Edit Lomba) --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> {{-- modal-lg untuk modal yang lebih lebar --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Memuat...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Konten modal akan dimuat oleh AJAX di sini --}}
                <p class="text-center">Memuat konten...</p>
            </div>
            {{-- Anda bisa menambahkan modal-footer di sini jika form AJAX Anda membutuhkannya secara statis --}}
            {{-- atau biarkan form yang dimuat AJAX yang menyediakan footer-nya sendiri --}}
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
@endpush