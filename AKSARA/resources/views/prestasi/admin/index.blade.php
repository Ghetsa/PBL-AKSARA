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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="form-label small">Filter Status Verifikasi:</label>
                                <select class="form-select form-select-sm" id="status" name="status">
                                    <option value="">- Pilih Status -</option>
                                    <option value="pending" selected>Pending</option>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tingkat" class="form-label small">Filter Tingkat Lomba:</label>
                                <select class="form-select form-select-sm" id="tingkat" name="tingkat">
                                    <option value="">- Semua Tingkat -</option>
                                    <option value="kota">Kota/Kabupaten</option>
                                    <option value="provinsi">Provinsi</option>
                                    <option value="nasional">Nasional</option>
                                    <option value="internasional">Internasional</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="kategori" class="form-label small">Filter Kategori Prestasi:</label>
                                <select class="form-select form-select-sm" id="kategori" name="kategori">
                                    <option value="">- Semua Kategori -</option>
                                    <option value="akademik">Akademik</option>
                                    <option value="non-akademik">Non-akademik</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row mb-3"> 
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Filter Status:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="status" name="status">
                                        <option value="">- Pilih Status -</option>
                                        <option value="pending" selected>Pending</option>
                                        <option value="disetujui">Disetujui</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                    <small class="form-text text-muted">Status Verifikasi</small>
                                </div>
                            </div>
                        </div>
                    </div>  --}}

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dt-responsive wrap" id="dataDaftarPrestasiAdmin" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>NIM</th>
                                    <th>Prestasi</th>
                                    <th>Kategori</th>
                                    <th>Tingkat</th>
                                    <th>Tahun</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
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
            responsive: true,
            ajax: {
                url: "{{ route('prestasi.admin.list') }}",
                data: function (d) {
                    d.status_verifikasi = $('#status').val();
                    d.tingkat = $('#tingkat').val();
                    d.kategori = $('#kategori').val();
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
                { data: 'status_verifikasi', name: 'status_verifikasi' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
        });

        // Trigger reload DataTables saat filter Status berubah
        $('#status, #tingkat, #kategori').on('change', function () {
            dataDaftarPrestasiAdmin.ajax.reload();
        });
    });
</script>
@endpush