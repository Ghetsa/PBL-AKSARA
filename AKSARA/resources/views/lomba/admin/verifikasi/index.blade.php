@extends('layouts.template') {{-- Sesuaikan dengan layout admin Anda --}}
@section('title', $breadcrumb->title ?? 'Manajemen Informasi Lomba')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">{{ $breadcrumb->title ?? 'Manajemen Informasi Lomba' }}</h3>
                    </div>
                <div class="card-body">
                    <div class="row mb-3 gx-2">
                        {{-- Filter Status Verifikasi --}}
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status_verifikasi_filter_admin" class="form-label small">Filter Status Verifikasi:</label>
                                <select class="form-select form-select-sm" id="status_verifikasi_filter_admin">
                                    <option value="">- Semua Status -</option>
                                    <option value="pending">Pending</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>
                        </div>
                        {{-- Filter Tingkat Lomba --}}
                        <div class="col-md-4">
                             <div class="form-group">
                                <label for="tingkat_lomba_filter_admin" class="form-label small">Filter Tingkat Lomba:</label>
                                <select class="form-select form-select-sm" id="tingkat_lomba_filter_admin">
                                    <option value="">- Semua Tingkat -</option>
                                    <option value="lokal">Lokal</option>
                                    <option value="nasional">Nasional</option>
                                    <option value="internasional">Internasional</option>
                                </select>
                            </div>
                        </div>
                        {{-- Bisa tambahkan filter lain jika perlu, misal berdasarkan penyelenggara atau bidang --}}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dt-responsive nowrap" id="dataLombaAdmin" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:5%;">No.</th>
                                    <th>Nama Lomba & Poster</th>
                                    <th>Penyelenggara</th>
                                    <th>Tingkat</th>
                                    <th>Batas Daftar</th>
                                    <th>Diinput Oleh</th>
                                    <th class="text-center">Status Verifikasi</th>
                                    <th class="text-center" style="width:15%;">Aksi</th>
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

{{-- Modal untuk Form Tambah/Edit Lomba oleh Admin --}}
<div class="modal fade" id="modalFormLombaAdmin" tabindex="-1" aria-labelledby="modalFormLombaAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            {{-- Konten AJAX form tambah/edit lomba dimuat di sini --}}
        </div>
    </div>
</div>

{{-- Modal untuk Form Verifikasi Lomba oleh Admin --}}
<div class="modal fade" id="modalVerifikasiLomba" tabindex="-1" aria-labelledby="modalVerifikasiLombaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            {{-- Konten AJAX form verifikasi lomba dimuat di sini --}}
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var dataTableLombaAdmin;

    function modalActionLombaAdmin(url, title = 'Form', modalId = 'modalFormLombaAdmin') { // Default ke modal form
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

    function deleteLomba(lombaId, namaLomba) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `Apakah Anda yakin ingin menghapus lomba: <br><strong>${namaLomba}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let url = `{{ route('admin.lomba.crud.destroy_ajax', ':id') }}`;
                url = url.replace(':id', lombaId);

                $.ajax({
                    url: url,
                    type: 'POST', // Method DELETE disimulasikan dengan _method
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            Swal.fire('Berhasil!', response.message, 'success');
                            dataTableLombaAdmin.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal!', response.message || 'Gagal menghapus data.', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan server.', 'error');
                    }
                });
            }
        });
    }


    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        dataTableLombaAdmin = $('#dataLombaAdmin').DataTable({
            processing: true, serverSide: true, responsive: true,
            ajax: {
                url: "{{ route('admin.lomba.verifikasi.list') }}",
                data: function (d) {
                    d.status_verifikasi_filter = $('#status_verifikasi_filter_admin').val();
                    d.tingkat_lomba_filter = $('#tingkat_lomba_filter_admin').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'nama_lomba' },
                { data: 'penyelenggara', name: 'penyelenggara' },
                { data: 'tingkat', name: 'tingkat', className: 'text-center' },
                { data: 'batas_pendaftaran', name: 'batas_pendaftaran', className: 'text-center' },
                { data: 'inputBy.nama', name: 'inputBy.nama' }, // Sorting/searching by penginput name
                { data: 'status_verifikasi', name: 'status_verifikasi', className: 'text-center' },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" }
        });

        $('#status_verifikasi_filter_admin, #tingkat_lomba_filter_admin').on('change', function () {
            dataTableLombaAdmin.ajax.reload();
        });
    });
</script>
@endpush