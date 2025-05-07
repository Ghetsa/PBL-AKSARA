@extends('layouts.template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Data Program Studi</h3>
                    <div class="card-tools">
                        <a href="{{ route('prodi.create') }}" class="btn btn-success btn-sm">Tambah</a>
                        <button type="button" class="btn btn-primary btn-sm" id="btn-tambah-prodi" data-bs-toggle="modal" data-bs-target="#modalCreateProdi">Tambah Ajax</button>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    {{-- <div class="row">
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
                    </div>     --}}
                    <table class="table table-bordered table-hover" id="table_prodi">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kode Prodi</th>
                                <th>Nama Prodi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
    @include('prodi.create_ajax')
</div>
@endsection

@push('js')
    <script>
        function modalAction() {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var dataProdi
        $(document).ready(function() {
            dataProdi = $('#table_prodi').DataTable({
                // serverSide: true, jika ingin menggunakan server side processing
                serverSide: true,
                ajax: {
                    "url": "{{ url('prodi/list') }}",
                    "dataType": "json",
                    "type": "POST",
                //     "data": function (d) { 
                //         d.role = $('#role').val(),
                //         d.status = $('#status').val();
                //      }
                },
                columns: [
                    {
                        data: "DT_RowIndex", // nomor urut dari laravel datatable addIndexColumn()
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },{
                        data: "kode",
                        className: "",
                        // orderable: true, jika ingin kolom ini bisa diurutkan
                        orderable: true,
                        // searchable: true, jika ingin kolom ini bisa dicari
                        searchable: true
                    },{
                        data: "nama",
                        className: "",
                        // orderable: true, jika ingin kolom ini bisa diurutkan
                        orderable: true,
                        // searchable: true, jika ingin kolom ini bisa dicari
                        searchable: true
                    },{
                        data: "aksi",
                        className: "",
                        orderable: false,   // orderable: true, jika ingin kolom ini bisa diurutkan
                        searchable: false   // searchable: true, jika ingin kolom ini bisa dicari
                    }
                ]
            });

            $('#role').on('change', function () { 
                dataProdi.ajax.reload();
            });

            $('#status').on('change', function () { 
                dataProdi.ajax.reload();
            });
            
        });
    </script>
@endpush