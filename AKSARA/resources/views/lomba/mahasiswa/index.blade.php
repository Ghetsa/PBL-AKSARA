@extends('layouts.template')
@section('title', 'Daftar Lomba')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Daftar Lomba</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm"
                            onclick="modalAction('{{ route('lomba.create') }}')">
                            Tambah Lomba
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Flash messages akan ditampilkan oleh SweetAlert --}}
                    <form method="GET" id="filterFormLomba" class="row g-3 mb-3 align-items-center">
                        <div class="col-md-4">
                            <label for="search_nama" class="form-label visually-hidden">Cari</label>
                            <input type="text" class="form-control form-control-sm" id="search_nama" name="search_nama"
                                placeholder="Cari Nama Prestasi/Mahasiswa/NIM..." value="{{ request('search_nama') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="filter_status" class="form-label visually-hidden">Status</label>
                            <select class="form-select form-select-sm" id="filter_status" name="filter_status">
                                <option value="">-- Semua Status --</option>
                                <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="disetujui" {{ request('filter_status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ request('filter_status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                        </div>

                        <div class="col-md-2 d-grid">
                            <a href="{{ route('lomba.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                        </div>

                        <div class="col-md-1 d-grid">
                            <button type="button" id="btnRekomendasi" class="btn btn-success btn-sm">
                                Rekomendasi
                            </button>
                        </div>
                    </form>

                    @if(request('rekomendasi'))
                        <div class="alert alert-info alert-sm p-2">
                            <strong>Info:</strong> Menampilkan daftar lomba berdasarkan hasil rekomendasi MOORA.
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataDaftarLomba" style="width:100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th>Nama Lomba</th>
                                    <th>Kategori</th>
                                    <th>Pembukaan Pendaftaran</th>
                                    <th>Penutupan Pendaftaran</th>
                                    <th class="text-center">Score Moora</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
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

{{-- Modal untuk form AJAX --}}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {{-- Konten modal AJAX --}}
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var dataDaftarLomba;
    var isRekomendasiAktif = false;

    function modalAction(url, modalId = 'myModal') {
        const targetModalContent = $(`#${modalId} .modal-content`);
        targetModalContent.html('');
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                targetModalContent.html(response);
                const modalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById(modalId));
                modalInstance.show();
            },
            error: function(xhr) {
                let errorMessage = 'Gagal memuat konten modal.';
                if (xhr.responseJSON && xhr.responseJSON.message)
                    errorMessage = xhr.responseJSON.message;
                Swal.fire({ icon: 'error', title: 'Error', text: errorMessage });
            }
        });
    }

    $(document).ready(function() {
        dataDaftarLomba = $('#dataDaftarLomba').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('lomba.list') }}",
                data: function(d) {
                    d.search_nama = $('#search_nama').val();
                    d.filter_status = $('#filter_status').val();
                    d.rekomendasi = isRekomendasiAktif ? 1 : 0;
                }
            },
            columns: [
                { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'nama_lomba' },
                { data: 'kategori' },
                { data: 'pembukaan_pendaftaran' },
                { data: 'batas_pendaftaran' },
                { data: 'moora_score', className: 'text-center' },
                { data: 'status_verifikasi', className: 'text-center' },
                { data: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
            }
        });

        $('#filterFormLomba').on('submit', function(e) {
            e.preventDefault();
            isRekomendasiAktif = false;
            dataDaftarLomba.ajax.reload();
        });

        $('#btnRekomendasi').on('click', function () {
            isRekomendasiAktif = true;
            Swal.fire({
                title: 'Mengambil rekomendasi...',
                text: 'Silakan tunggu sejenak.',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            dataDaftarLomba.ajax.reload(function () {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Rekomendasi dimuat!',
                    text: 'Hasil rekomendasi MOORA telah ditampilkan.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
        });
    });
</script>
@endpush
