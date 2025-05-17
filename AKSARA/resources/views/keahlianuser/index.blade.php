@extends('layouts.template')
@section('title', 'Histori Keahlian Saya')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Histori Keahlian Saya</h3>
                    <button type="button" class="btn btn-primary btn-sm"
                            onclick="modalAction('{{ route('mahasiswa.keahlianuser.create') }}', 'Tambah Keahlian')">
                        <i class="fas fa-plus"></i> Tambah Keahlian
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataKeahlianMahasiswa" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No.</th>
                                    <th>Nama User</th>
                                    <th>Keahlian</th>
                                    <th>Sertifikasi</th>
                                    <th>Status Verifikasi</th>
                                    <th class="text-center">Aksi</th> {{-- Lebar disesuaikan --}}
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

{{-- Modal Umum --}}
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
    var dataKeahlianMahasiswa;

    function modalAction(url, modalTitle = 'Form Aksi') {
        $('#myModal .modal-content').html('<div class="modal-body text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'); // Loading state
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#myModal .modal-content').html(response);
                const modalInstance = new bootstrap.Modal(document.getElementById('myModal'));
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
        dataKeahlianMahasiswa = $('#dataKeahlianMahasiswa').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('mahasiswa.keahlianuser.list') }}", 
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'user.name', name: 'user.name' }, {{-- Nama User --}}
                { data: 'keahlian.nama', name: 'keahlian.nama' }, {{-- Nama Keahlian --}}
                { data: 'sertifikasi', name: 'sertifikasi' }, {{-- Sertifikasi --}}
                { data: 'status_verifikasi_badge', name: 'status_verifikasi', className: 'text-center' }, {{-- Badge Status Verifikasi --}}
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
        });

        // Event delegation untuk tombol delete (jika ada di dalam 'aksi' dari server-side)
        $('body').on('click', '.btn-delete-keahlian', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const namaKeahlian = $(this).data('nama') || 'keahlian ini';
            Swal.fire({
                title: 'Anda Yakin?',
                text: `Ingin menghapus ${namaKeahlian}? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE"
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Terhapus!', response.message, 'success');
                                dataKeahlianMahasiswa.ajax.reload();
                            } else {
                                Swal.fire('Gagal!', response.message || 'Gagal menghapus keahlian.', 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghapus.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
