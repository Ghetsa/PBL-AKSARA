@extends('layouts.template')
@section('title', 'Daftar Mahasiswa Bimbingan')

@section('content')
    <div class="container">
        <h2>Prestasi Mahasiswa Bimbingan</h2>

        @if($prestasi->isEmpty())
            <p>Tidak ada data prestasi mahasiswa bimbingan.</p>
        @else
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nama Mahasiswa</th>
                        <th>Nama Prestasi</th>
                        <th>Kategori</th>
                        <th>Bidang</th>
                        <th>Tingkat</th>
                        <th>Tahun</th>
                        <th>Status Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prestasi as $item)
                        <tr>
                            <td>{{ $item->mahasiswa->user->nama }}</td>
                            <td>{{ $item->nama_prestasi }}</td>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->bidang->nama_bidang ?? '-' }}</td>
                            <td>{{ $item->tingkat }}</td>
                            <td>{{ $item->tahun }}</td>
                            <td>{{ ucfirst($item->status_verifikasi) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection