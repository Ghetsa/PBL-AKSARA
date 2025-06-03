@extends('layouts.template')
@section('title', $breadcrumb->title ?? 'Mahasiswa Bimbingan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $breadcrumb->title ?? 'Mahasiswa Bimbingan' }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataBimbingan" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%;">No.</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Nama Prestasi</th>
                                        <th>Kategori</th>
                                        <th>Bidang</th>
                                        <th>Tingkat</th>
                                        <th>Tahun</th>
                                        <th>Status Verifikasi</th>
                                        <th class="text-center" style="width: 15%;">Aksi</th>
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

    {{-- Modal AJAX --}}
    <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                {{-- Konten AJAX dimuat di sini --}}
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    function modalAction(url) {
        $('#myModal .modal-content').html(`
            <div class="modal-body text-center">
                <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        `);

        $.get(url, function (res) {
            $('#myModal .modal-content').html(res);
            new bootstrap.Modal(document.getElementById('myModal')).show();
        }).fail(function () {
            Swal.fire('Gagal', 'Tidak dapat memuat konten.', 'error');
        });
    }

    function submitVerification(formId) {
        const form = $(`#${formId}`);
        const url = form.attr('action');
        const data = form.serialize();

        $.post(url, data)
            .done(function (res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#dataBimbingan').DataTable().ajax.reload();
                bootstrap.Modal.getInstance(document.getElementById('myModal')).hide();
            })
            .fail(function () {
                Swal.fire('Gagal', 'Verifikasi gagal diproses.', 'error');
            });
    }

    $(document).ready(function () {
        const table = $('#dataBimbingan').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('bimbingan.list') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'mahasiswa_nama', name: 'mahasiswa.user.nama' },
                { data: 'nama_prestasi', name: 'nama_prestasi' },
                { data: 'kategori', name: 'kategori' },
                { data: 'bidang_nama', name: 'bidang.nama' },
                { data: 'tingkat', name: 'tingkat' },
                { data: 'tahun', name: 'tahun', className: 'text-center' },
                { data: 'status_verifikasi', name: 'status_verifikasi', className: 'text-center' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' },
            ],
        });
    });
</script>
@endpush
