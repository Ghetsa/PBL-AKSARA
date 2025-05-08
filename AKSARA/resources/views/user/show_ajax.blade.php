@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            @empty($user)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
                    Data yang Anda cari tidak ditemukan.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @else
                <table class="table table-sm table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th class="text-end">Role User</th>
                            <td>{{ $user->role }}</td>
                        </tr>
                        <tr>
                            <th class="text-end">Nama</th>
                            <td>{{ $user->nama }}</td>
                        </tr>
                        <tr>
                            <th class="text-end">Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th class="text-end">Status</th>
                            <td>{{ $user->status }}</td>
                        </tr>
                    </tbody>
                </table>
            @endempty
            <a href="{{ url('user') }}" class="btn btn-sm btn-secondary mt-2">Kembali</a>
        </div>
    </div>
@endsection

@push('css')
@endpush
@push('js')
@endpush
