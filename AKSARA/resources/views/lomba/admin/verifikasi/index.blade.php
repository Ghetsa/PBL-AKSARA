@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Verifikasi Pengajuan Lomba')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">{{ $breadcrumb->title ?? 'Daftar Pengajuan Info Lomba' }}</h3>
                    {{-- Tombol Tambah Lomba oleh admin tidak ada di sini, tapi di halaman CRUD Admin --}}
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_verifikasi_filter" class="form-label small">Filter Status Verifikasi:</label>
                                <select class="form-select form-select-sm" id="status_verifikasi_filter">
                                    <option value="">- Semua Status -</option>
                                    <option value="pending" selected>Pending</option> 
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
                        <table class="table table-bordered table-hover dt-responsive wrap" id="tableVerifikasiLombaAdmin" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:5%;">No.</th>
                                    <th>Nama Lomba</th>
                                    <th>Diajukan Oleh</th>
                                    <th>Tingkat</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
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

{{-- Modal untuk Form Verifikasi Lomba oleh Admin --}}
<div class="modal fade" id="modalVerifikasiLombaAdmin" tabindex="-1" aria-labelledby="modalVerifikasiLombaAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            {{-- Konten AJAX form verifikasi lomba dimuat di sini --}}
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var dataTableVerifikasiLomba; // Pastikan nama variabel unik jika ada DataTable lain di layout

    function modalActionLombaAdmin(url, title = 'Form Verifikasi', modalId = 'modalVerifikasiLombaAdmin') {
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

    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        dataTableVerifikasiLomba = $('#tableVerifikasiLombaAdmin').DataTable({
            processing: true, serverSide: true, responsive: true,
            ajax: {
                url: "{{ route('admin.lomba.verifikasi.list') }}", // Route untuk list data verifikasi
                data: function (d) {
                    d.status_verifikasi_filter = $('#status_verifikasi_filter').val(); // ID filter disesuaikan
                    d.tingkat_lomba_filter = $('#tingkat_lomba_filter_verifikasi').val(); // ID filter disesuaikan
                    d.kategori_lomba_filter = $('#kategori_lomba_filter_verifikasi').val(); // ID filter disesuaikan
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba_display', name: 'nama_lomba' }, // Menggunakan kolom gabungan
                { data: 'diajukan_oleh', name: 'inputBy.nama', orderable: false, searchable: true }, // 'penginput.nama' untuk server-side search
                { data: 'tingkat', name: 'tingkat', render: function(data, type, row) { return data.charAt(0).toUpperCase() + data.slice(1); } }, // Menggunakan kolom gabungan
                { data: 'status_verifikasi', name: 'status_verifikasi' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
            order: [[ 3, "desc" ]] // Default order by tanggal pengajuan (created_at)
        });

        $('#status_verifikasi_filter, #tingkat_lomba_filter_verifikasi, #kategori_lomba_filter_verifikasi').on('change', function () {
            dataTableVerifikasiLomba.ajax.reload();
        });

        // Inisialisasi tooltip Bootstrap jika ada di halaman ini (misalnya dari tombol 'Ditolak')
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endpush