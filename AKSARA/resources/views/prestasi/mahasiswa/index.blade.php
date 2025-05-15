@extends('layouts.template')
@section('title', 'Histori Prestasi Saya')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Histori Prestasi Saya</h3>
                    <button type="button" class="btn btn-primary btn-sm"
                            onclick="modalAction('{{ route('prestasi.mahasiswa.create_ajax') }}')">
                        <i class="fas fa-plus"></i> Tambah Prestasi Baru (AJAX)
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataPrestasiMahasiswa" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No.</th>
                                    <th class="text-center">Nama Prestasi/Kegiatan</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Tingkat</th>
                                    <th class="text-center">Tahun</th>
                                    <th class="text-center">Dosen Pembimbing</th>
                                    <th class="text-center">Status Verifikasi</th>
                                    <th class="text-center">Bukti</th>
                                    <th class="text-center" style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Diisi oleh DataTables --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {{-- AJAX response masuk di sini --}}
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var dataPrestasiMahasiswa;

    function modalAction(url, modalTitle = 'Form Aksi') {
        $('#myModal .modal-content').html('');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#myModal .modal-content').html(response);
                const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('myModal'));
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
        dataPrestasiMahasiswa = $('#dataPrestasiMahasiswa').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('prestasi.mahasiswa.list') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_prestasi', name: 'nama_prestasi' },
                { data: 'kategori', name: 'kategori' },
                { data: 'tingkat', name: 'tingkat' },
                { data: 'tahun', name: 'tahun', className: 'text-center' },
                { data: 'dosen', name: 'dosen', className: 'text-center' },
                { data: 'status_verifikasi', name: 'status_verifikasi', className: 'text-center' },
                { data: 'file_bukti_action', name: 'file_bukti_action', className: 'text-center', orderable: false, searchable: false },
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: { url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" }
        });
    });
</script>
@endpush
