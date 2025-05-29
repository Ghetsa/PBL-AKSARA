@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Manajemen Data Lomba')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">{{ $breadcrumb->title ?? 'Manajemen Data Lomba' }}</h3>
                    <button class="btn btn-sm btn-primary" onclick="modalActionLombaAdminCrud('{{ route('admin.lomba.crud.create_form_ajax') }}', 'Tambah Info Lomba Baru Oleh Admin', 'modalFormLombaAdminCrud')">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Lomba (Admin)
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3 gx-2">
                        <div class="col-md-4">
                            <label for="status_filter_crud" class="form-label small">Filter Status Verifikasi:</label>
                            <select class="form-select form-select-sm" id="status_filter_crud">
                                <option value="">- Semua Status -</option>
                                <option value="pending">Pending</option>
                                <option value="disetujui">Disetujui</option>
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
                                <th class="text-center">No.</th>
                                <th>Nama Lomba & Poster</th>
                                <th>Penyelenggara</th>
                                <th>Tingkat</th>
                                <th>Batas Daftar</th>
                                <th>Diinput Oleh</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Form Tambah/Edit Lomba oleh Admin (CRUD) --}}
<div class="modal fade" id="modalFormLombaAdminCrud" tabindex="-1" aria-labelledby="modalFormLombaAdminCrudLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content"></div>
    </div>
</div>
@endsection

@push('js')
<script>
    var dtLombaCrudAdmin;

    function modalActionLombaAdminCrud(url, title = 'Form Lomba', modalId = 'modalFormLombaAdminCrud') {
        const targetModal = $(`#${modalId}`);
        const targetModalContent = targetModal.find('.modal-content');
        targetModalContent.html('<div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat...</p></div>');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
        modalInstance.show();
        $.ajax({
            url: url, type: 'GET',
            success: function (response) { targetModalContent.html(response); },
            error: function (xhr) { /* ... error handling ... */ }
        });
    }

    function deleteLombaCrud(lombaId, namaLomba) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Yakin ingin menghapus lomba: <strong>${namaLomba}</strong>?`,
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = `{{ route('admin.lomba.crud.destroy_ajax', ':id') }}`.replace(':id', lombaId);
                $.ajax({
                    url: url, type: 'POST', data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            dtLombaCrudAdmin.ajax.reload(null, false);
                        } else { Swal.fire('Gagal!', response.message || 'Gagal menghapus.', 'error'); }
                    },
                    error: function(xhr) { Swal.fire('Error!', 'Terjadi kesalahan.', 'error'); }
                });
            }
        });
    }

    $(document).ready(function() {
        dtLombaCrudAdmin = $('#dataTableLombaCrudAdmin').DataTable({
            processing: true, serverSide: true, responsive: true,
            ajax: {
                url: "{{ route('admin.lomba.crud.list') }}",
                data: function (d) {
                    d.status_verifikasi_filter_crud = $('#status_filter_crud').val();
                    d.tingkat_lomba_filter_crud = $('#tingkat_filter_crud').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'nama_lomba' },
                { data: 'penyelenggara', name: 'penyelenggara' },
                { data: 'tingkat', name: 'tingkat', className: 'text-center' },
                { data: 'batas_pendaftaran', name: 'batas_pendaftaran', className: 'text-center' }, // Format di controller
                { data: 'penginput_nama', name: 'penginput.nama' },
                { data: 'status_verifikasi', name: 'status_verifikasi', className: 'text-center' },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
        });
        $('#status_filter_crud, #tingkat_filter_crud').on('change', function () { dtLombaCrudAdmin.ajax.reload(); });
    });
</script>
@endpush