@extends('layouts.template') 
@section('title', 'Verifikasi Keahlian Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pengajuan Keahlian Mahasiswa</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        {{-- Filter Status Verifikasi --}}
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label" for="status_filter_keahlian">Filter Status:</label>
                                <div class="col-sm-8">
                                    <select class="form-select" id="status_filter_keahlian" name="status_filter_keahlian">
                                        <option value="">- Semua Status -</option>
                                        <option value="pending">Pending</option>
                                        <option value="disetujui">Disetujui</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dt-responsive wrap" id="dataDaftarKeahlianAdmin" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width:5%;">No.</th>
                                    <th>Nama Pengguna</th>
                                    <th>Bidang Keahlian</th>
                                    <th>Sertifikat</th>
                                    <th>Lembaga</th>
                                    {{-- <th>Tanggal Perolehan</th>
                                    <th>Tanggal Kadaluarsa</th> --}}
                                    <th>Status</th>
                                    <th style="width:10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Verifikasi Keahlian --}}
<div class="modal fade" id="modalAdminKeahlian" tabindex="-1" role="dialog" aria-labelledby="modalAdminKeahlianLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content">
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var dataTableKeahlianAdmin;

    function modalActionKeahlian(url, modalId = 'modalAdminKeahlian') {
        const targetModal = $(`#${modalId}`);
        const targetModalContent = targetModal.find('.modal-content');
        
        targetModalContent.html('<div class="modal-body text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Memuat konten...</p></div>');
        const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
        modalInstance.show();

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                targetModalContent.html(response);
            },
            error: function (xhr) {
                let errorMessage = 'Gagal memuat konten modal.';
                if(xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                targetModalContent.html(`<div class="modal-header"><h5 class="modal-title">Error</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p class="text-danger">${errorMessage}</p></div>`);
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        dataTableKeahlianAdmin = $('#dataDaftarKeahlianAdmin').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('keahlian_user.admin.list') }}", // Pastikan route ini ada
                data: function (d) {
                    d.status_verifikasi = $('#status_filter_keahlian').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'user_nama', name: 'user.nama' }, // Sesuaikan dengan alias di controller
                { data: 'bidang_nama', name: 'bidang.bidang_nama' },
                { data: 'nama_sertifikat', name: 'nama_sertifikat' },
                { data: 'lembaga_sertifikasi', name: 'lembaga_sertifikasi' },
                // { data: 'tanggal_perolehan_sertifikat', name: 'tanggal_perolehan_sertifikat' },
                // { data: 'tanggal_kadaluarsa_sertifikat', name: 'tanggal_kadaluarsa_sertifikat' },
                { data: 'status_verifikasi', name: 'status_verifikasi' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ]
        });

        $('#status_filter_keahlian').on('change', function () {
            dataTableKeahlianAdmin.ajax.reload();
        });
    });
</script>
@endpush