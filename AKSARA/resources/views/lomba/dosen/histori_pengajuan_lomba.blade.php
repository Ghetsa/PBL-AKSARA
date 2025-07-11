@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Histori Pengajuan Lomba Saya')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h3 class="card-title mb-0">{{ $breadcrumb->title ?? 'Histori Pengajuan Lomba Saya' }}</h3>
                    {{-- Tombol "Ajukan Info Lomba" bisa juga diletakkan di halaman utama lomba.index --}}
                    <div class="card-tools d-flex flex-wrap gap-1 mt-2 mt-md-0">
                        <button class="btn btn-sm btn-success" onclick="modalActionLomba('{{ route('lomba.dosen.create_form') }}', 'Ajukan Info Lomba Baru', 'modalFormLombaUser')">
                            <i class="fas fa-plus-circle me-1"></i> Ajukan Info Lomba Baru
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-light-info bg-light-info text-info border-0" role="alert">
                        <i class="fas fa-history me-2"></i>Halaman ini menampilkan riwayat pengajuan informasi lomba yang telah Anda submit beserta status verifikasinya.
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_verifikasi_filter" class="form-label small">Filter Status Verifikasi:</label>
                                <select class="form-select form-select-sm" id="status_verifikasi_filter">
                                    <option value="">- Semua Status -</option>
                                    <option value="pending">Pending</option> 
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
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
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dt-responsive wrap" id="dataHistoriLombaUser" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No.</th>
                                    <th>Nama Lomba</th>
                                    <th>Penyelenggara</th>
                                    <th>Tingkat</th>
                                    <th>Batas Pendaftaran</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Status Verifikasi</th>
                                    <th>Catatan/Aksi</th>
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

{{-- Modal untuk Form Pengajuan/Edit Lomba oleh Mahasiswa/Dosen (jika ada edit) --}}
<div class="modal fade" id="modalFormLombaUser" tabindex="-1" aria-labelledby="modalFormLombaUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            {{-- Konten AJAX form pengajuan/edit lomba dimuat di sini --}}
        </div>
    </div>
</div>

{{-- Modal untuk Detail Lomba (jika diperlukan di sini) --}}
{{-- <div class="modal fade" id="modalDetailLombaPublik" tabindex="-1" aria-labelledby="modalDetailLombaPublikLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div> --}}
@endsection

@push('js')
<script>
    var historiTable
    // Fungsi modalActionLomba bisa Anda pindahkan ke file JS global jika digunakan di banyak tempat
    // Pastikan modalId dikirim dengan benar
    function modalActionLomba(url, title = 'Form', modalId = 'modalFormLombaUser') { // Default ke modal form
        const targetModal = $(`#${modalId}`);
        const targetModalContent = targetModal.find('.modal-content');
        
        targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-3 fs-5">Memuat...</p></div>');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
        modalInstance.show();

        $.ajax({
            url: url, type: 'GET',
            success: function (response) { targetModalContent.html(response); },
            error: function (xhr) {
                let msg = xhr.responseJSON?.message ?? 'Gagal memuat konten.';
                targetModalContent.html(`<div class="modal-header"><h5 class="modal-title">${title}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${msg}</p></div>`);
            }
        });
    }

    $(document).ready(function () {
        historiTable = $('#dataHistoriLombaUser').DataTable({
            processing: true, serverSide: true, responsive: true,
            ajax: {
                url: "{{ route('lomba.dosen.histori.list') }}", // Route untuk list data verifikasi
                data: function (d) {
                    d.status_verifikasi_filter = $('#status_verifikasi_filter').val(); // ID filter disesuaikan
                    d.tingkat_lomba_filter = $('#tingkat_lomba_filter_verifikasi').val(); // ID filter disesuaikan
                    d.kategori_lomba_filter = $('#kategori_lomba_filter_verifikasi').val(); // ID filter disesuaikan
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'nama_lomba' },
                // { data: 'bidang_lomba', name: 'bidang_lomba' },
                { data: 'penyelenggara', name: 'penyelenggara' },
                { data: 'tingkat', name: 'tingkat', render: function(data, type, row) { return data.charAt(0).toUpperCase() + data.slice(1); } },
                { data: 'batas_pendaftaran', name: 'batas_pendaftaran' },
                { data: 'created_at', name: 'created_at' }, // Tanggal pengajuan
                { data: 'status_verifikasi', name: 'status_verifikasi' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        $('#status_verifikasi_filter, #tingkat_lomba_filter_verifikasi, #kategori_lomba_filter_verifikasi').on('change', function () {
            historiTable.ajax.reload();
        });

        // Inisialisasi tooltip Bootstrap jika ada
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush