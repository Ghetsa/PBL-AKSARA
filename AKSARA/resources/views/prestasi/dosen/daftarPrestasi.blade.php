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
                                <a href="{{ route('prestasi.dosen.index') }}"
                                    class="btn btn-secondary btn-sm w-100">Reset</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataDaftarPrestasiDosen"
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
                                        {{-- Kolom Aksi dihilangkan --}}
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

    {{-- Modal tetap bisa dihapus kalau tidak dipakai --}}
    <div class="modal fade" id="myModalDosen" tabindex="-1" role="dialog" aria-labelledby="myModalDosenLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {{-- Konten modal akan dimuat di sini --}}
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        var dataDaftarPrestasiDosen;

        function modalAction(url, modalId = 'myModalDosen') { // Default ke myModalDosen
            const targetModalContent = $(`#${modalId} .modal-content`);
            targetModalContent.html(''); // Kosongkan dulu
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
                    if (xhr.responseJSON && xhr.responseJSON.message) errorMessage = xhr.responseJSON.message;
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        }

        $(document).ready(function() {
            dataDaftarPrestasiDosen = $('#dataDaftarPrestasiDosen').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('prestasi.dosen.list') }}",
                    data: function(d) {
                        d.search_nama = $('#search_nama_dosen').val();
                        d.filter_status = $('#filter_status_dosen').val();
                    }
                },
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
                    },
                    {
                        data: 'nim_mahasiswa',
                        name: 'mahasiswa.nim'
                    },
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
                    }
                    // kolom aksi dihapus
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            $('#filterFormDosenPrestasi').on('submit', function(e) {
                e.preventDefault();
                dataDaftarPrestasiDosen.ajax.reload();
            });
        });
    </script>
@endpush
