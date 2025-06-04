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
                        {{-- Flash messages akan ditampilkan oleh SweetAlert --}}
                        <form method="GET" id="filterFormDosenPrestasi" class="row g-3 mb-3 align-items-center">
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" id="search_nama_dosen"
                                    name="search_nama" placeholder="Cari Nama Prestasi/Mahasiswa/NIM..."
                                    value="{{ request('search_nama') }}">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select form-select-sm" id="filter_status_dosen" name="filter_status">
                                    <option value="">-- Semua Status --</option>
                                    <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="disetujui"
                                        {{ request('filter_status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('filter_status') == 'ditolak' ? 'selected' : '' }}>
                                        Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('bimbingan.index') }}" class="btn btn-secondary btn-sm w-100">Reset</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataDaftarBimbinganDosen"
                                style="width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>NIM</th>
                                        <th>Nama Prestasi</th>
                                        <th>Kategori</th>
                                        <th>Tingkat</th>
                                        <th>Tahun</th>
                                        <th>Dosen Pembimbing</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- DataTable akan mengisi ini --}}
                                </tbody>
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
        var dataDaftarBimbinganDosen;

        function modalAction(url) {
            $('#myModal .modal-content').html(`
            <div class="modal-body text-center">
                <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
            </div>
        `);

            $.get(url, function(res) {
                $('#myModal .modal-content').html(res);
                new bootstrap.Modal(document.getElementById('myModal')).show();
            }).fail(function() {
                Swal.fire('Gagal', 'Tidak dapat memuat konten.', 'error');
            });
        }

        $(document).ready(function() {
            const table = $('#dataBimbingan').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('bimbingan.list') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_mahasiswa',
                        name: 'mahasiswa.user.nama'
                    }, // Untuk searching di server-side
                    {
                        data: 'nim_mahasiswa',
                        name: 'mahasiswa.nim'
                    }, // Untuk searching di server-side
                    {
                        data: 'nama_prestasi',
                        name: 'nama_prestasi'
                    },
                    {
                        data: 'kategori',
                        name: 'kategori'
                    },
                    {
                        data: 'tingkat',
                        name: 'tingkat'
                    },
                    {
                        data: 'tahun',
                        name: 'tahun'
                    },
                    {
                        data: 'dosen',
                        name: 'dosen'
                    },
                    {
                        data: 'status_verifikasi',
                        name: 'status_verifikasi',
                        className: 'text-center'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    }
                ],
            });
            $('#filterFormDosenPrestasi').on('submit', function(e) {
                e.preventDefault();
                dataDaftarPrestasiDosen.ajax.reload();
            });
        });
    </script>
@endpush
