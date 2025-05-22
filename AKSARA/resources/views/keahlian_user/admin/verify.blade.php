{{-- @extends('layouts.app')

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
@endsection --}}

{{-- Asumsi ini adalah halaman full, bukan modal. Jika modal, sesuaikan strukturnya. --}}
@extends('layouts.template') {{-- atau layout admin Anda --}}
@section('title', 'Verifikasi Keahlian User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Verifikasi Keahlian User</h4>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nama Pengguna</dt>
                        <dd class="col-sm-8">{{ $data->user->nama ?? '-' }}</dd>

                        <dt class="col-sm-4">Bidang Keahlian</dt>
                        <dd class="col-sm-8">{{ $data->bidang->bidang_nama ?? '-' }}</dd>

                        <dt class="col-sm-4">Nama Sertifikat/Keahlian</dt>
                        <dd class="col-sm-8">{{ $data->nama_sertifikat ?? '-' }}</dd>

                        <dt class="col-sm-4">Lembaga Sertifikasi</dt>
                        <dd class="col-sm-8">{{ $data->lembaga_sertifikasi ?? '-' }}</dd>

                        <dt class="col-sm-4">Tanggal Perolehan</dt>
                        <dd class="col-sm-8">{{ $data->tanggal_perolehan_sertifikat ? $data->tanggal_perolehan_sertifikat->format('d F Y') : '-' }}</dd>
                        
                        <dt class="col-sm-4">Tanggal Kadaluarsa</dt>
                        <dd class="col-sm-8">{{ $data->tanggal_kadaluarsa_sertifikat ? $data->tanggal_kadaluarsa_sertifikat->format('d F Y') : '-' }}</dd>

                        <dt class="col-sm-4">File Bukti/Sertifikat</dt>
                        <dd class="col-sm-8">
                            @if($data->sertifikasi && Storage::disk('public')->exists($data->sertifikasi))
                                <a href="{{ asset('storage/' . $data->sertifikasi) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Lihat File
                                </a>
                            @else
                                <span class="text-muted">Tidak ada file.</span>
                            @endif
                        </dd>
                    </dl>
                    <hr>
                    <form id="formVerifikasiKeahlian" action="{{ route('keahlian_user.prosesVerifikasi', $data->keahlian_user_id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="status_verifikasi" class="form-label">Status Verifikasi <span class="text-danger">*</span></label>
                            <select name="status_verifikasi" id="status_verifikasi" class="form-select" required>
                                <option value="pending" {{ old('status_verifikasi', $data->status_verifikasi) == 'pending' ? 'selected' : '' }}>Menunggu</option>
                                <option value="disetujui" {{ old('status_verifikasi', $data->status_verifikasi) == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="ditolak" {{ old('status_verifikasi', $data->status_verifikasi) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="catatan_verifikasi" class="form-label">Catatan Verifikasi (Opsional, wajib jika ditolak)</label>
                            <textarea name="catatan_verifikasi" id="catatan_verifikasi" class="form-control" rows="3">{{ old('catatan_verifikasi', $data->catatan_verifikasi) }}</textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.keahlian_user.index') }}" class="btn btn-secondary me-2">Kembali</a> {{-- Ganti dengan route list admin yang sesuai --}}
                            <button type="submit" class="btn btn-primary">Simpan Verifikasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
// Script untuk validasi catatan jika ditolak (opsional)
$('#formVerifikasiKeahlian').on('submit', function(e) {
    const status = $('#status_verifikasi').val();
    const catatan = $('#catatan_verifikasi').val().trim();
    if (status === 'ditolak' && catatan === '') {
        e.preventDefault();
        Swal.fire('Peringatan', 'Catatan verifikasi wajib diisi jika status ditolak.', 'warning');
    }
});
</script>
@endpush