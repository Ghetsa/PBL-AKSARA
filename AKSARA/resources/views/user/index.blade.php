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
                    <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm">Tambah</a>
                    <button type="button" class="btn btn-primary btn-sm" id="btn-tambah-user" data-bs-toggle="modal" data-bs-target="#modalCreateUser">
                        Tambah Ajax
                    </button>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-1 control-label col-form-label">Filter Role:</label>
                                <div class="col-3">
                                    <select class="form-control" id="role" name="role" required>
                                        <option value="">- Semua -</option>
                                        <option value="mahasiswa">Mahasiswa</option>
                                        <option value="dosen">Dosen</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                    <small class="form-text text-muted">Role Pengguna</small>
                                </div>
                            </div>
                        </div>
                    </div>    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group row">
                                <label class="col-1 control-label col-form-label">Filter Status:</label>
                                <div class="col-3">
                                    <select class="form-control" id="status" name="status" required>
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
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @include('user.create_ajax')
</div>
@endsection

@push('js')
    <script>
        function modalAction() {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var dataUser
        $(document).ready(function() {
            dataUser = $('#table_user').DataTable({
                // serverSide: true, jika ingin menggunakan server side processing
                serverSide: true,
                ajax: {
                    "url": "{{ url('user/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) { 
                        d.role = $('#role').val(),
                        d.status = $('#status').val();
                     }
                },
                columns: [
                    {
                        data: "DT_RowIndex", // nomor urut dari laravel datatable addIndexColumn()
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },{
                        data: "nama",
                        className: "",
                        // orderable: true, jika ingin kolom ini bisa diurutkan
                        orderable: true,
                        // searchable: true, jika ingin kolom ini bisa dicari
                        searchable: true
                    },{
                        data: "email",
                        className: "",
                        orderable: true,
                        searchable: true
                    },{
                        // mengambil data level hasil dari ORM berelasi
                        data: "role",
                        className: "",
                        orderable: false,
                        searchable: false
                    },{
                        // mengambil data level hasil dari ORM berelasi
                        data: "status",
                        className: "",
                        orderable: false,
                        searchable: false
                    },{
                        data: "aksi",
                        className: "",
                        orderable: false,   // orderable: true, jika ingin kolom ini bisa diurutkan
                        searchable: false   // searchable: true, jika ingin kolom ini bisa dicari
                    }
                ]
            });

            $('#role').on('change', function () { 
                dataUser.ajax.reload();
            });

            $('#status').on('change', function () { 
                dataUser.ajax.reload();
            });
            
        });
    </script>
@endpush