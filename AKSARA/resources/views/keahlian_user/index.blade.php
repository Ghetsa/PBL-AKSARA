@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Keahlian Saya') {{-- Tambahkan default title --}}

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ $breadcrumb->title ?? 'Keahlian Saya' }}</h3>
                        <button class="btn btn-sm btn-primary" onclick="tambahKeahlian()"><i class="fas fa-plus-circle"></i> Tambah Keahlian</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataKeahlianUser" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%;">No.</th>
                                        <th>Bidang Keahlian</th>
                                        <th>Sertifikasi</th>
                                        <th>Lembaga Sertifikasi</th>
                                        <th>Tanggal Perolehan</th>
                                        <th>Tanggal Kadaluarsa</th>
                                        <th>Status Verifikasi</th>
                                        <th class="text-center" style="width: 15%;">Aksi</th> {{-- Sesuaikan width jika perlu --}}
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

    {{-- Modal untuk Tambah/Edit/Detail --}}
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg"> {{-- atau modal-xl jika kontennya banyak --}}
            <div class="modal-content">
                {{-- Konten AJAX akan dimuat di sini --}}
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    function tambahKeahlian() {
        $.get("{{ route('keahlian_user.create') }}", function (res) {
            $('#myModal .modal-content').html(res);
            new bootstrap.Modal(document.getElementById('myModal')).show();
        }).fail(function () {
            Swal.fire('Gagal', 'Tidak dapat memuat konten.', 'error');
        });
    }

    function modalAction(url, title = 'Form') {
        $('#myModal .modal-content').html(`
            <div class="modal-body text-center">
                <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        `);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                $('#myModal .modal-content').html(response);
                new bootstrap.Modal(document.getElementById('myModal')).show();
            },
            error: function () {
                Swal.fire('Gagal', 'Tidak dapat memuat konten.', 'error');
            }
        });
    }

    $(document).ready(function () {
        // Tambahkan CSRF token untuk semua request AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const table = $('#dataKeahlianUser').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('keahlian_user.list') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'bidang_nama', name: 'bidang.bidang_nama' }, // Untuk searching/ordering di server
                { data: 'nama_sertifikat', name: 'nama_sertifikat' },
                { data: 'lembaga_sertifikasi', name: 'lembaga_sertifikasi' },
                { data: 'tanggal_perolehan_sertifikat', name: 'tanggal_perolehan_sertifikat', className: 'text-center' },
                { data: 'tanggal_kadaluarsa_sertifikat', name: 'tanggal_kadaluarsa_sertifikat', className: 'text-center' },
                { data: 'status_verifikasi', name: 'status_verifikasi', className: 'text-center' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ],
        });

        // Fungsi delete Anda sudah ada, pastikan _token dikirim.
        $('body').on('click', '.btn-delete-keahlian', function () {
            const url = $(this).data('url');
            const nama = $(this).data('nama') ?? 'keahlian ini';

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: `Data ${nama} akan dihapus permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (res) {
                            Swal.fire('Berhasil', res.message, 'success');
                            table.ajax.reload();
                        },
                        error: function () {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush