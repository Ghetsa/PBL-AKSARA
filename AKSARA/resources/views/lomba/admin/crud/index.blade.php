@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Manajemen Data Lomba')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">{{ $breadcrumb->title ?? 'Manajemen Data Lomba' }}</h3>
                    <div class="menu">
                        <a href="{{ route('lomba.export.pdf') }}" class="btn btn-sm btn-warning"><i class="fa fa-file-pdf"></i> Export Lomba (PDF)</a>
                        <a href="{{ route('lomba.export.excel') }}" class="btn btn-sm btn-success"><i class="fa fa-file-excel"></i> Export Lomba</a>
                        <button class="btn btn-sm btn-primary" onclick="modalActionLombaAdminCrud('{{ route('admin.lomba.crud.create_form_ajax') }}', 'Tambah Info Lomba Baru', 'modalFormLombaAdminCrud')">
                            <i class="fas fa-plus-circle me-1"></i> Tambah Lomba
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="tingkat_filter_crud" class="form-label">Filter Tingkat Lomba:</label>
                            <select class="form-select form-select-sm" id="tingkat_filter_crud">
                                <option value="">- Semua Tingkat -</option>
                                <option value="lokal">Lokal</option>
                                <option value="nasional">Nasional</option>
                                <option value="internasional">Internasional</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="kategori_filter_crud" class="form-label">Filter Kategori Lomba:</label>
                            <select class="form-select form-select-sm" id="kategori_filter_crud">
                                <option value="">- Semua Kategori -</option>
                                <option value="individu">Individu</option>
                                <option value="kelompok">Kelompok</option>
                            </select>
                        </div>
                    </div>

                    <table class="table table-bordered table-hover dt-responsive nowrap" id="dataTableLombaCrudAdmin" style="width:100%;">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:5%;">No.</th>
                                <th>Nama Lomba</th>
                                <th>Penyelenggara</th>
                                <th>Tingkat</th>
                                <th>Batas Daftar</th>
                                <th class="text-center">Kategori</th>
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
<div class="modal fade" role="dialog" id="modalFormLombaAdminCrud" tabindex="-1" aria-labelledby="modalFormLombaAdminCrudLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                    d.tingkat_lomba_filter_crud = $('#tingkat_filter_crud').val();
                    d.kategori_lomba_filter_crud = $('#kategori_filter_crud').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'nama_lomba' },
                { data: 'penyelenggara', name: 'penyelenggara' },
                { data: 'tingkat', name: 'tingkat', className: 'text-center' },
                { data: 'batas_pendaftaran', name: 'batas_pendaftaran', className: 'text-center' },
                { data: 'kategori', name: 'kategori', className: 'text-center' },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            order: [[ 4, "desc" ]] // Default order by batas_pendaftaran (atau created_at jika lebih sesuai)
        });

        $('#tingkat_filter_crud, #kategori_filter_crud').on('change', function () { 
            dtLombaCrudAdmin.ajax.reload(); 
        });
    });
</script>
@endpush