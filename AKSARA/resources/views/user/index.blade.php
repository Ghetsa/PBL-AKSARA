@extends('layouts.template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            {{-- Menampilkan flash message dari session --}}
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
                        {{-- Tombol Tambah User (non-AJAX) --}}
                        <a href="{{ route('user.create') }}" class="btn btn-success btn-sm">Tambah</a>
                        {{-- Tombol Tambah User (AJAX) - Memanggil modalAction --}}
                        <button type="button" class="btn btn-primary btn-sm"
                                onclick="modalAction('{{ route('user.create_ajax') }}')">Tambah Ajax</button>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Filter Section - Dibuat sejajar --}}
                    <div class="row mb-3"> {{-- Tambahkan margin bottom --}}
                        {{-- Filter Role --}}
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

                        {{-- Filter Status --}}
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
                    </div> {{-- End Filter Row --}}

                    {{-- DataTables Table --}}
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
                            {{-- DataTables akan mengisi tbody --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    {{-- STRUKTUR MODAL UTAMA UNTUK SEMUA MODAL AJAX --}}
    {{-- Ini adalah satu-satunya elemen modal di halaman --}}
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" id="modal-master"> {{-- Optional: Anda bisa beri ID pada dialog jika perlu --}}
            <div class="modal-content">
                {{-- Konten modal (header, body, footer, form) akan dimuat di sini via AJAX --}}
            </div>
        </div>
    </div>

@endsection

@push('js')
    {{-- Pastikan library jQuery, Bootstrap JS, DataTables JS, jQuery Validation, SweetAlert2 sudah dimuat sebelumnya di layouts.template --}}

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
@endpush
