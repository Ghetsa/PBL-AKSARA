@extends('layouts.app')

@section('content')
<h4>Verifikasi Keahlian User</h4>
<p><strong>User:</strong> {{ $data->user->name ?? '-' }}</p>
<p><strong>Keahlian:</strong> {{ $data->keahlian->nama ?? '-' }}</p>
<p><strong>Sertifikasi:</strong> {{ $data->sertifikasi ?? '-' }}</p>
<form action="{{ route('keahlianuser.process_verify', $data->keahlian_user_id) }}" method="POST">
    @csrf
    <div class="mb-3">
        <label>Status Verifikasi</label>
        <select name="status_verifikasi" class="form-control" required>
            <option value="pending" {{ $data->status_verifikasi == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="disetujui" {{ $data->status_verifikasi == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
            <option value="ditolak" {{ $data->status_verifikasi == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
        </select>
    </div>
    <div class="mb-3">
        <label>Catatan Verifikasi (opsional)</label>
        <textarea name="catatan_verifikasi" class="form-control">{{ $data->catatan_verifikasi }}</textarea>
    </div>
    <button type="submit" class="btn btn-success">Simpan Verifikasi</button>
</form>
@endsection
