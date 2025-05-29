@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Manajemen Data Lomba')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">{{ $breadcrumb->title ?? 'Manajemen Data Lomba' }}</h3>
                    <button class="btn btn-sm btn-primary" onclick="modalActionLombaAdminCrud('{{ route('admin.lomba.crud.create_form_ajax') }}', 'Tambah Info Lomba Baru', 'modalFormLombaAdminCrud')">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Lomba
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3 gx-2">
                        <div class="col-md-4">
                            <label for="status_filter_crud" class="form-label small">Filter Status Verifikasi:</label>
                            <select class="form-select form-select-sm" id="status_filter_crud">
                                <option value="">- Semua Status -</option>
                                <option value="disetujui" selected>Disetujui</option> {{-- Default ke Disetujui --}}
                                <option value="pending">Pending</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tingkat_filter_crud" class="form-label small">Filter Tingkat Lomba:</label>
                            <select class="form-select form-select-sm" id="tingkat_filter_crud">
                                <option value="">- Semua Tingkat -</option>
                                <option value="lokal">Lokal</option>
                                <option value="nasional">Nasional</option>
                                <option value="internasional">Internasional</option>
                            </select>
                        </div>
                    </div>

                    <table class="table table-bordered table-hover dt-responsive nowrap" id="dataTableLombaCrudAdmin" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:5%;">No.</th>
                                <th>Nama Lomba & Poster</th>
                                <th>Penyelenggara</th>
                                <th>Tingkat</th>
                                <th>Batas Daftar</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width:18%;">Aksi</th> {{-- Lebar disesuaikan untuk 3 tombol --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Form Tambah/Edit Lomba --}}
<div class="modal fade" id="modalFormLombaAdminCrud" tabindex="-1" aria-labelledby="modalFormLombaAdminCrudLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>

{{-- Modal untuk Detail Lomba (menggunakan modal ID yang berbeda agar tidak konflik) --}}
<div class="modal fade" id="modalDetailLombaAdminCrud" tabindex="-1" aria-labelledby="modalDetailLombaAdminCrudLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>

{{-- Modal untuk Konfirmasi Hapus Lomba --}}
<div class="modal fade" id="modalConfirmDeleteLombaAdminCrud" tabindex="-1" aria-labelledby="modalConfirmDeleteLombaAdminCrudLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content"></div>
    </div>
</div>
@endsection

@push('js')
<script>
    var dtLombaCrudAdmin;

    // Pastikan fungsi modalActionLombaAdminCrud bisa menangani ID modal yang berbeda
    function modalActionLombaAdminCrud(url, title = 'Form Lomba', modalId = 'modalFormLombaAdminCrud') {
        const targetModal = $(`#${modalId}`); // Gunakan modalId yang dikirim
        const targetModalContent = targetModal.find('.modal-content');
        targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat...</p></div>');
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

    function deleteLombaCrud(lombaId, namaLomba) {
        // URL untuk mengambil konten modal konfirmasi
        let confirmUrl = `{{ route('admin.lomba.crud.confirm_delete_ajax', ':id') }}`;
        confirmUrl = confirmUrl.replace(':id', lombaId);

        // Panggil fungsi modalAction untuk memuat konten konfirmasi ke modal
        // Pastikan Anda menggunakan ID modal yang benar untuk konfirmasi hapus
        modalActionLombaAdminCrud(confirmUrl, `Konfirmasi Hapus Lomba: ${namaLomba}`, 'modalConfirmDeleteLombaAdminCrud');
    }

    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        dtLombaCrudAdmin = $('#dataTableLombaCrudAdmin').DataTable({
            processing: true, serverSide: true, responsive: true,
            ajax: {
                url: "{{ route('admin.lomba.crud.list') }}", // Pastikan route ini benar
                data: function (d) {
                    // Mengambil nilai dari filter, jika kosong, controller akan default ke 'disetujui'
                    d.status_verifikasi_filter_crud = $('#status_filter_crud').val(); 
                    d.tingkat_lomba_filter_crud = $('#tingkat_filter_crud').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'nama_lomba' },
                { data: 'penyelenggara', name: 'penyelenggara' },
                { data: 'tingkat', name: 'tingkat', className: 'text-center' },
                { data: 'batas_pendaftaran', name: 'batas_pendaftaran', className: 'text-center' },
                { data: 'status_verifikasi', name: 'status_verifikasi', className: 'text-center' },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" },
            order: [[ 4, "desc" ]] // Default order by batas_pendaftaran (atau created_at jika lebih sesuai)
        });

        $('#status_filter_crud, #tingkat_filter_crud').on('change', function () { 
            dtLombaCrudAdmin.ajax.reload(); 
        });
    });
</script>
@endpush