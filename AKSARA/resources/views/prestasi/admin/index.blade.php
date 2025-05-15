@extends('layouts.template')
@section('title', 'Verifikasi Prestasi Mahasiswa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pengajuan Prestasi Mahasiswa</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3"> 
                        {{-- Filter Status Verifikasi --}}
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Filter Status:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="status" name="status">
                                        <option value="">- Pilih Status -</option>
                                        <option value="pending">Pending</option>
                                        <option value="disetujui">Disetujui</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                    <small class="form-text text-muted">Status Verifikasi</small>
                                </div>
                            </div>
                        </div>
                    </div> 

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataDaftarPrestasiAdmin" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>NIM</th>
                                    <th>Nama Prestasi</th>
                                    <th>Kategori</th>
                                    <th>Tingkat</th>
                                    <th>Tahun</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
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

<div class="modal fade" id="myModalAdmin" tabindex="-1" role="dialog" aria-labelledby="myModalAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var dataDaftarPrestasiAdmin;

    function modalAction(url, modalId = 'myModalAdmin') { // Default ke myModalAdmin
        const targetModalContent = $(`#${modalId} .modal-content`);
        targetModalContent.html(''); // Kosongkan dulu
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                targetModalContent.html(response);
                const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
                modalInstance.show();
            },
            error: function (xhr) {
                let errorMessage = 'Gagal memuat konten modal.';
                if(xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Error', text: errorMessage });
            }
        });
    }

    $(document).ready(function() {
        dataDaftarPrestasiAdmin = $('#dataDaftarPrestasiAdmin').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('prestasi.admin.list') }}",
                data: function (d) {
                    d.status_verifikasi = $('#status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_mahasiswa', name: 'mahasiswa.user.nama' }, // Untuk searching di server-side
                { data: 'nim_mahasiswa', name: 'mahasiswa.nim' },       // Untuk searching di server-side
                { data: 'nama_prestasi', name: 'nama_prestasi' },
                { data: 'kategori', name: 'kategori' },
                { data: 'tingkat', name: 'tingkat' },
                { data: 'tahun', name: 'tahun' },
                { data: 'status_verifikasi', name: 'status_verifikasi', className: 'text-center' },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" }
        });

        // Trigger reload DataTables saat filter Status berubah
        $('#status').on('change', function () {
            dataDaftarPrestasiAdmin.ajax.reload();
        });
    });
</script>
@endpush