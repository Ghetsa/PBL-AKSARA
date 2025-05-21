@extends('layouts.template')
@section('title', $breadcrumb->title)

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">{{ $breadcrumb->title }}</h3>
                        <button class="btn btn-sm btn-primary" onclick="tambahKeahlian()">Tambah Keahlian</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataKeahlianUser" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%;">No.</th>
                                        <th>Bidang Keahlian</th>
                                        <th>Sertifikasi</th>
                                        <th>Status Verifikasi</th>
                                        <th class="text-center" style="width: 20%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Data dimuat oleh DataTables AJAX --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk Tambah/Edit --}}
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
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
        const table = $('#dataKeahlianUser').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('keahlian_user.list') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'bidang_nama', name: 'bidang.bidang_nama' },
                { data: 'sertifikasi', name: 'sertifikasi' },
                { data: 'status_verifikasi', name: 'status_verifikasi' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
            ],
        });

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
