@extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Laporan & Analisis')

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    {{-- Tambahan CSS jika diperlukan --}}
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $breadcrumb->title }}</h1>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Prestasi Disetujui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPrestasiDisetujui }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-award fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Lomba Disetujui</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalLombaDisetujui }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Mahasiswa Berprestasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $mahasiswaBerprestasiCount }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Prestasi per Tahun</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="prestasiPerTahunChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h4 class="m-0 font-weight-bold text-primary">Laporan Data Prestasi Mahasiswa (Disetujui)</h4>
            <div class="export">
                <a href="{{ route('prestasi.export.excel') }}" class="btn btn-success shadow"><i class="ph-duotone ph-microsoft-excel-logo text-white-50"></i> Export Excel</a>
                <a href="{{ route('prestasi.export.pdf') }}" class="btn btn-warning shadow"><i class="ph-duotone ph-file-pdf text-white-50"></i> Export PDF</a>
            </div>
            </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filter_tahun_akademik" class="form-label">Tahun Akademik:</label>
                    <select id="filter_tahun_akademik" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunAkademikList as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter_kategori_lomba" class="form-label">Kategori Lomba:</label>
                    <select id="filter_kategori_lomba" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                         @foreach($kategoriLombaList as $kategori)
                        <option value="{{ $kategori }}">{{ ucfirst($kategori) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter_tingkat_kompetisi" class="form-label">Tingkat Kompetisi:</label>
                    <select id="filter_tingkat_kompetisi" class="form-select form-select-sm">
                        <option value="">Semua Tingkat</option>
                         @foreach($tingkatKompetisiList as $tingkat)
                        <option value="{{ $tingkat }}">{{ ucfirst($tingkat) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTablePrestasi" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Mahasiswa</th>
                            <th>Dosen Pembimbing</th>
                            <th>NIM & Prodi</th>
                            <th>Nama Prestasi</th>
                            <th>Tingkat</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            {{-- <th>Penyelenggara</th> --}}
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h4 class="m-0 font-weight-bold text-primary">Laporan Data Lomba (Disetujui)</h4>
            <div class="export">
                <a href="{{ route('lomba.export.excel') }}" class="btn btn-success shadow"><i class="ph-duotone ph-microsoft-excel-logo text-white-50"></i> Export Excel</a>
                <a href="{{ route('lomba.export.pdf') }}" class="btn btn-warning shadow"><i class="ph-duotone ph-file-pdf text-white-50"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filter_tahun_lomba" class="form-label">Tahun Lomba (Dibuat):</label>
                     <select id="filter_tahun_lomba" class="form-select form-select-sm">
                        <option value="">Semua Tahun</option>
                        @foreach($tahunAkademikList as $tahun) {{-- Bisa pakai tahun yang sama atau query baru --}}
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter_kategori_lomba_main" class="form-label">Kategori Lomba:</label>
                    <select id="filter_kategori_lomba_main" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        <option value="individu">Individu</option>
                        <option value="kelompok">Kelompok</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="filter_tingkat_lomba_main" class="form-label">Tingkat Lomba:</label>
                    <select id="filter_tingkat_lomba_main" class="form-select form-select-sm">
                        <option value="">Semua Tingkat</option>
                        <option value="lokal">Lokal</option>
                        <option value="nasional">Nasional</option>
                        <option value="internasional">Internasional</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableLomba" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lomba</th>
                            <th>Penyelenggara</th>
                            <th>Tingkat</th>
                            <th>Kategori</th>
                            <th>Batas Daftar</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('js')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    {{-- DataTables Buttons --}}
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>

    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script> {{-- Pastikan path benar --}}


    <script>
    $(document).ready(function() {
        // DataTables untuk Prestasi
        var tablePrestasi = $('#dataTablePrestasi').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.laporan.prestasi.data') }}",
                data: function (d) {
                    d.filter_tahun_akademik = $('#filter_tahun_akademik').val();
                    d.filter_kategori_lomba = $('#filter_kategori_lomba').val();
                    d.filter_tingkat_kompetisi = $('#filter_tingkat_kompetisi').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'mahasiswa', name: 'mahasiswa.user.nama', orderable: false }, // Search akan kompleks, bisa di-disable
                { data: 'mahasiswa', name: 'mahasiswa.nim', visible: false, searchable: true }, // Untuk search by NIM
                { data: 'dosen', name: 'dosen.user.nama', visible: false, searchable: true }, // Untuk search by NIM
                { data: 'nama_prestasi', name: 'nama_prestasi' },
                { data: 'tingkat', name: 'tingkat' },
                { data: 'kategori', name: 'kategori' },
                { data: 'penyelenggara', name: 'penyelenggara' }
            ],
            // dom: 'Bfrtip', // Untuk menampilkan tombol export
            // buttons: [
            //     'copy', 'excel', 'pdf', 'print'
            // ]
        });

        $('#filter_tahun_akademik, #filter_kategori_lomba, #filter_tingkat_kompetisi').change(function() {
            tablePrestasi.ajax.reload();
            updateExportPrestasiLink();
        });
        
        function updateExportPrestasiLink() {
            let baseUrl = "{{ route('admin.laporan.prestasi.export') }}";
            let params = {
                filter_tahun_akademik: $('#filter_tahun_akademik').val(),
                filter_kategori_lomba: $('#filter_kategori_lomba').val(),
                filter_tingkat_kompetisi: $('#filter_tingkat_kompetisi').val()
            };
            $('#exportPrestasiBtn').attr('href', baseUrl + '?' + $.param(params));
        }
        updateExportPrestasiLink(); // Panggil saat awal


        // DataTables untuk Lomba
        var tableLomba = $('#dataTableLomba').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.laporan.lomba.data') }}",
                 data: function (d) {
                    d.filter_tahun_lomba = $('#filter_tahun_lomba').val();
                    d.filter_kategori_lomba_main = $('#filter_kategori_lomba_main').val();
                    d.filter_tingkat_lomba_main = $('#filter_tingkat_lomba_main').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_lomba', name: 'nama_lomba' },
                { data: 'penyelenggara', name: 'penyelenggara' },
                { data: 'tingkat', name: 'tingkat' },
                { data: 'kategori', name: 'kategori' },
                { data: 'batas_pendaftaran', name: 'batas_pendaftaran' }
            ],
        });
        
        $('#filter_tahun_lomba, #filter_kategori_lomba_main, #filter_tingkat_lomba_main').change(function() {
            tableLomba.ajax.reload();
            updateExportLombaLink();
        });

        function updateExportLombaLink() {
            let baseUrl = "{{ route('admin.laporan.lomba.export') }}";
            let params = {
                filter_tahun_lomba: $('#filter_tahun_lomba').val(),
                filter_kategori_lomba_main: $('#filter_kategori_lomba_main').val(),
                filter_tingkat_lomba_main: $('#filter_tingkat_lomba_main').val()
            };
            $('#exportLombaBtn').attr('href', baseUrl + '?' + $.param(params));
        }
        updateExportLombaLink(); // Panggil saat awal

    });
    </script>
@endpush