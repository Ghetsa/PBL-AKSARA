@extends('layouts.template')

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

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h3 class="card-title mb-0">Data Program Studi</h3>
                    <div class="card-tools d-flex flex-wrap gap-1 mt-2 mt-md-0">
                        <a href="{{ route('prodi.export.pdf') }}" class="btn btn-sm btn-warning"><i class="fa fa-file-pdf"></i> Export Prodi (PDF)</a>
                        <a href="{{ route('prodi.export.excel') }}" class="btn btn-sm btn-success"><i class="fa fa-file-excel"></i> Export Prodi</a>
                        <button type="button" class="btn btn-primary btn-sm" onclick="modalAction('{{ route('prodi.create') }}')"><i class="fas fa-plus-circle"></i> Tambah</button>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-hover dt-responsive nowrap" id="table_prodi" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:10%;">No.</th>
                                <th>Kode Prodi</th>
                                <th>Nama Prodi</th>
                                <th style="width:15%;">Aksi</th>
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
    
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" id="modal-master"> {{-- Optional: Anda bisa beri ID pada dialog jika perlu --}}
            <div class="modal-content">
                {{-- Konten modal (header, body, footer, form) akan dimuat di sini via AJAX --}}
            </div>
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
        var dataProdi;

        $(document).ready(function () {
            // Inisialisasi DataTables
            dataProdi = $('#table_prodi').DataTable({
                serverSide: true, // Menggunakan server-side processing
                processing: true,
                responsive: true,
                ajax: {
                    "url": "{{ url('prodi/list') }}", // URL untuk mengambil data
                    "dataType": "json",
                    "type": "POST"
                },
                columns: [
                    {
                        data: "DT_RowIndex", // Nomor urut dari Laravel DataTables
                        className: "text-center",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "kode",
                        className: "text-wrap",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "nama",
                        className: "text-wrap",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "aksi", // Kolom aksi berisi tombol-tombol
                        className: "",
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        }); 

        // Fungsi untuk membuka modal konfirmasi hapus (memanggil modalAction)
        function deleteConfirmAjax(prodi_id) {
            modalAction(`{{ url('/prodi') }}/${prodi_id}/confirm_ajax`);
            // Atau menggunakan route helper:
            // modalAction(`{{ route('prodi.confirm_ajax', ':id') }}`.replace(':id', prodi_id));
        }

    </script>
@endpush
