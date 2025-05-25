{{-- @extends('layouts.template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Data User</h3>
                    <div class="card-tools">
                        <a href="{{ route('user.create') }}" class="btn btn-success btn-sm">Tambah</a>
                        <button type="button" class="btn btn-primary btn-sm"
                                onclick="modalAction('{{ route('user.create_ajax') }}')">
                                <i class="fas fa-plus-circle"></i> Tambah Ajax</button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3"> 
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Filter Role:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="role" name="role">
                                        <option value="">- Semua -</option>
                                        <option value="admin">Admin</option>
                                        <option value="dosen">Dosen</option>
                                        <option value="mahasiswa">Mahasiswa</option>
                                    </select>
                                    <small class="form-text text-muted">Role Pengguna</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Filter Status:</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="status" name="status">
                                        <option value="">- Semua -</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="nonaktif">Nonaktif</option>
                                    </select>
                                    <small class="form-text text-muted">Status Pengguna</small>
                                </div>
                            </div>
                        </div>
                    </div> 

                    <table class="table table-bordered table-hover" id="table_user">
                        <thead>
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" id="modal-master"> 
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        // Fungsi untuk memuat konten AJAX ke dalam modal utama (#myModal)
        function modalAction(url) {
            // Kosongkan konten modal sebelumnya sebelum memuat yang baru
            $('#myModal .modal-content').html('');

            $.ajax({
                url: url,
                type: 'GET', // Biasanya GET untuk menampilkan form/data
                success: function (response) {
                    // Muat konten yang diterima dari response (HTML dari view) ke dalam modal-content
                    $('#myModal .modal-content').html(response);
                    // Tampilkan modal Bootstrap
                    $('#myModal').modal('show');
                },
                error: function (xhr) {
                    // Tangani error jika gagal memuat konten modal
                    let errorMessage = 'Gagal memuat konten modal. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const jsonError = JSON.parse(xhr.responseText);
                            if (jsonError.message) errorMessage = jsonError.message;
                        } catch (e) {
                            errorMessage = 'Error: ' + xhr.status + ' ' + xhr.statusText;
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        }

        // Variabel global untuk DataTables agar bisa diakses dari script di dalam modal
        var dataUser;

        $(document).ready(function () {
            // Inisialisasi DataTables
            dataUser = $('#table_user').DataTable({
                serverSide: true, // Menggunakan server-side processing
                ajax: {
                    "url": "{{ url('user/list') }}", // URL untuk mengambil data
                    "dataType": "json",
                    "type": "POST", // Menggunakan method POST
                    "data": function (d) {
                        // Mengirim data filter ke backend
                        d.role = $('#role').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex", // Nomor urut dari Laravel DataTables
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "email",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "role",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "status",
                        className: "",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi", // Kolom aksi berisi tombol-tombol
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Trigger reload DataTables saat filter Role berubah
            $('#role').on('change', function () {
                dataUser.ajax.reload();
            });

            // Trigger reload DataTables saat filter Status berubah
            $('#status').on('change', function () {
                dataUser.ajax.reload();
            });

        }); // End document ready

        // Fungsi untuk membuka modal konfirmasi hapus (memanggil modalAction)
        function deleteConfirmAjax(user_id) {
            modalAction(`{{ url('/user') }}/${user_id}/confirm_ajax`);
            // Atau menggunakan route helper:
            // modalAction(`{{ route('user.confirm_ajax', ':id') }}`.replace(':id', user_id));
        }

    </script>
@endpush --}}

@extends('layouts.template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            {{-- Flash messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Data User</h3>
                    <div class="card-tools">
                        {{-- <a href="{{ route('user.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Tambah (Non-AJAX)</a> --}}
                        <button type="button" class="btn btn-primary btn-sm"
                                onclick="modalAction('{{ route('user.create_ajax') }}')">
                                <i class="fas fa-plus-circle"></i> Tambah User</button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="role_filter" class="form-label">Filter Role:</label>
                            <select class="form-select form-select-sm" id="role_filter" name="role_filter">
                                <option value="">- Semua Role -</option>
                                <option value="admin">Admin</option>
                                <option value="dosen">Dosen</option>
                                <option value="mahasiswa">Mahasiswa</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="status_filter" class="form-label">Filter Status:</label>
                            <select class="form-select form-select-sm" id="status_filter" name="status_filter">
                                <option value="">- Semua Status -</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <table class="table table-bordered table-hover dt-responsive nowrap" id="table_user" style="width:100%">
                        <thead>
                            <tr>
                                <th class="text-center" style="width:5%;">No.</th>
                                {{-- Hapus kolom Foto --}}
                                <th>Nama</th> {{-- Kolom ini akan berisi foto, nama, dan detail (NIP/NIM) --}}
                                <th>Email</th>
                                <th>No. Telepon</th>
                                {{-- Kolom alamat bisa ditambahkan jika diperlukan, atau tampilkan di detail --}}
                                {{-- <th>Alamat</th> --}}
                                <th class="text-center">Role</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width:15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- Modal utama --}}
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" id="modal-master">
            <div class="modal-content"></div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        var dataUser; 
        function modalAction(url) {
            // Kosongkan konten modal sebelumnya sebelum memuat yang baru
            $('#myModal .modal-content').html('');

            $.ajax({
                url: url,
                type: 'GET', // Biasanya GET untuk menampilkan form/data
                success: function (response) {
                    // Muat konten yang diterima dari response (HTML dari view) ke dalam modal-content
                    $('#myModal .modal-content').html(response);
                    // Tampilkan modal Bootstrap
                    $('#myModal').modal('show');
                },
                error: function (xhr) {
                    // Tangani error jika gagal memuat konten modal
                    let errorMessage = 'Gagal memuat konten modal. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            const jsonError = JSON.parse(xhr.responseText);
                            if (jsonError.message) errorMessage = jsonError.message;
                        } catch (e) {
                            errorMessage = 'Error: ' + xhr.status + ' ' + xhr.statusText;
                        }
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        }

        $(document).ready(function () {
            dataUser = $('#table_user').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    "url": "{{ route('user.list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.role = $('#role_filter').val();
                        d.status = $('#status_filter').val();
                    }
                },
                columns: [
                    { data: "DT_RowIndex", className: "text-center", orderable: false, searchable: false },
                    // Hapus kolom 'foto_display'
                    { data: "nama_dan_detail", name: "nama", orderable: true, searchable: true }, // Gunakan 'nama_dan_detail' dari controller
                    { data: "email", name: "email", orderable: true, searchable: true },
                    { data: "no_telepon", name: "no_telepon", orderable: false, searchable: true },
                    // { data: "alamat", name: "alamat", orderable: false, searchable: true }, // Uncomment jika ingin menampilkan alamat
                    { data: "role", name: "role", className: "text-center", orderable: true, searchable: true },
                    { data: "status", name: "status", className: "text-center", orderable: true, searchable: true },
                    { data: "aksi", className: "text-center", orderable: false, searchable: false }
                ]
            });

            $('#role_filter, #status_filter').on('change', function () {
                dataUser.ajax.reload();
            });
        });

        function deleteConfirmAjax(user_id) {
                modalAction(`{{ url('/user') }}/${user_id}/confirm_ajax`);
                // Atau menggunakan route helper:
                // modalAction(`{{ route('user.confirm_ajax', ':id') }}`.replace(':id', user_id));
        }
        
    </script>
@endpush