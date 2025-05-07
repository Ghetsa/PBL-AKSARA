@extends('layouts.template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Data User</h3>
                    <div class="card-tools">
                        <a href="{{ route('user.create') }}" class="btn btn-success btn-sm">Tambah</a>
                        <button type="button" class="btn btn-primary btn-sm"
                            onclick="modalAction('{{ route('user.create_ajax') }}')">Tambah Ajax</button>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Filter Role:</label>
                                <div class="col-sm-8"> {{-- Sesuaikan lebar kolom input --}}
                                    <select class="form-control" id="role" name="role">
                                        <option value="">- Semua -</option>
                                        <option value="mahasiswa">Mahasiswa</option>
                                        <option value="dosen">Dosen</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <small class="form-text text-muted">Role Pengguna</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-4 col-form-label">Filter Status:</label>
                                <div class="col-sm-8"> {{-- Sesuaikan lebar kolom input --}}
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
                    </table>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" id="modal-master">
            <div class="modal-content">
                {{-- Konten modal (header, body, footer) akan dimuat di sini via AJAX --}}
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        function modalAction(url) {
            $('#myModal .modal-content').html('');

            $.ajax({
                url: url,
                type: 'GET',
                success: function (response) {

                    $('#myModal .modal-content').html(response);
                    $('#myModal').modal('show');
                },
                error: function (xhr) {
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


        var dataUser;
        $(document).ready(function () {
            dataUser = $('#table_user').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('user/list') }}", // Gunakan url helper atau route helper jika perlu
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.role = $('#role').val();
                        d.status = $('#status').val();
                    }
                },
                columns: [
                    {
                        data: "user_id",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }, {
                        data: "nama",
                        className: "",
                        orderable: true,
                        searchable: true
                    }, {
                        data: "email",
                        className: "",
                        orderable: true,
                        searchable: true
                    }, {
                        data: "role",
                        className: "",
                        orderable: true, // Sesuaikan jika kolom ini perlu diurutkan
                        searchable: true // Sesuaikan jika kolom ini perlu dicari
                    }, {
                        data: "status",
                        className: "",
                        orderable: true, // Sesuaikan jika kolom ini perlu diurutkan
                        searchable: true // Sesuaikan jika kolom ini perlu dicari
                    }, {
                        data: "aksi",
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Trigger reload DataTables saat filter berubah
            $('#role, #status').on('change', function () {
                dataUser.ajax.reload();
            });

        });

        function deleteConfirmAjax(user_id) {
            modalAction(`{{ url('/user') }}/${user_id}/confirm_ajax`); // Gunakan url helper dan string template
        }

    </script>
@endpush