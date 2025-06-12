@extends('layouts.template')

@section('title', $breadcrumb->title ?? 'Dashboard Dosen')

@push('css')
    {{-- CSS untuk Chart.js jika diperlukan --}}
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ $breadcrumb->title }}</h1>
        </div>

        {{-- Menampilkan 3 Kartu Statistik --}}
        <div class="row">

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jumlah Mahasiswa Bimbingan</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahMahasiswaBimbingan }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                {{-- Judul diubah sesuai permintaan --}}
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Jumlah Prestasi Mahasiswa</div>
                                {{-- Variabel diubah menjadi total keseluruhan --}}
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahPrestasiKeseluruhan }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-award fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Lomba (Disetujui)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $jumlahLombaDisetujui }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-trophy fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {{-- Akhir baris kartu statistik --}}
    </div>
@endsection

@push('js')
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Data untuk Lomba Berdasarkan Tingkat         
        });
    </script>
@endpush
