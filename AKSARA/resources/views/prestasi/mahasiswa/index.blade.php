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
                            onclick="modalAction('{{ route('prestasi.mahasiswa.create_ajax') }}', 'Upload Prestasi Baru')">
                        <i class="fas fa-plus-circle"></i> Upload Prestasi Baru
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataPrestasiMahasiswa" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">No.</th>
                                    <th>Prestasi</th>
                                    <th>Bidang</th>
                                    <th>Kategori</th>
                                    <th>Tingkat</th>
                                    <th>Tahun</th>
                                    <th>Dosen Pembimbing</th> {{-- Kolom Baru --}}
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 15%;">Aksi</th> {{-- Lebar disesuaikan --}}
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
    <div class="modal-dialog modal-lg" role="document"> {{-- Bisa modal-xl jika form edit/detail butuh lebih banyak ruang --}}
        <div class="modal-content">
            {{-- AJAX response masuk di sini --}}
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var dataPrestasiMahasiswa;

    // Fungsi modalAction yang sudah ada, mungkin tambahkan parameter judul modal
    function modalAction(url, modalTitle = 'Form Aksi') {
        $('#myModal .modal-content').html('<div class="modal-body text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>'); // Loading state
        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#myModal .modal-content').html(response);
                // Judul modal bisa diatur dari dalam view yang di-load, atau di sini jika view tidak punya modal-header
                // Jika view yang di-load sudah ada modal-header, baris di bawah ini bisa dikomentari
                // $('#myModal .modal-header .modal-title').text(modalTitle);
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
        dataPrestasiMahasiswa = $('#dataPrestasiMahasiswa').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('prestasi.mahasiswa.list') }}", 
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_prestasi', name: 'nama_prestasi' },
                { data: 'bidang_nama', name: 'bidang_nama' },
                { data: 'kategori', name: 'kategori' },
                { data: 'tingkat', name: 'tingkat' },
                { data: 'tahun', name: 'tahun' },
                { data: 'dosen_pembimbing', name: 'dosen_pembimbing.nama', orderable: false, searchable: false }, 
                { data: 'status_verifikasi_badge', name: 'status_verifikasi', className: 'text-center' }, // Akan berisi HTML badge
                { data: 'aksi', name: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
        });

        // Event delegation untuk tombol delete (jika ada di dalam 'aksi' dari server-side)
        $('body').on('click', '.btn-delete-prestasi', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const namaPrestasi = $(this).data('nama') || 'prestasi ini';
            Swal.fire({
                title: 'Anda Yakin?',
                text: `Ingin menghapus ${namaPrestasi}? Tindakan ini tidak dapat dibatalkan.`,
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
                        type: 'POST', // atau DELETE, sesuaikan dengan method di route Anda
                        data: {
                            _token: "{{ csrf_token() }}",
                            _method: "DELETE" // Jika route Anda menggunakan method DELETE
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Terhapus!', response.message, 'success');
                                dataPrestasiMahasiswa.ajax.reload();
                            } else {
                                Swal.fire('Gagal!', response.message || 'Gagal menghapus prestasi.', 'error');
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