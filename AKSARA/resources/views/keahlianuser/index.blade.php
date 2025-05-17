@extends('layouts.app')

@section('content')
<h4>{{ $breadcrumb->title }}</h4>
<a href="{{ route('keahlianuser.create') }}" class="btn btn-primary mb-3">Tambah Keahlian</a>

<table class="table" id="table-keahlianuser">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama User</th>
            <th>Keahlian</th>
            <th>Sertifikasi</th>
            <th>Status Verifikasi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
$(document).ready(function() {
    $('#table-keahlianuser').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('keahlianuser.list') }}',
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama_user', name: 'user.name'},
            {data: 'keahlian', name: 'keahlian.nama'},
            {data: 'sertifikasi', name: 'sertifikasi'},
            {data: 'status_verifikasi', name: 'status_verifikasi'},
            {data: 'aksi', name: 'aksi', orderable: false, searchable: false}
        ]
    });
});
</script>
@endsection
