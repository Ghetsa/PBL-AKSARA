@extends('layouts.template')

@section('content')
<h4>Edit Keahlian</h4>

<form action="{{ route('mahasiswa.keahlianuser.update', $keahlianUser->keahlian_user_id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="keahlian_id">Keahlian</label>
        <select id="keahlian_id" name="keahlian_id" class="form-control" required>
            <option value="">-- Pilih Keahlian --</option>
            @foreach ($keahlians as $k)
                <option value="{{ $k->keahlian_id }}" {{ $keahlianUser->keahlian_id == $k->keahlian_id ? 'selected' : '' }}>
                    {{ $k->keahlian_nama ?? 'Unknown' }}
                </option>
            @endforeach
        </select>
        @error('keahlian_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3">
        <label for="sertifikasi">Sertifikasi (optional)</label>
        <input id="sertifikasi" type="file" name="sertifikasi" class="form-control" />
        @if ($keahlianUser->sertifikasi)
            <small>
                File saat ini: 
                <a href="{{ asset('storage/' . $keahlianUser->sertifikasi) }}" target="_blank" rel="noopener noreferrer">
                    Lihat Sertifikasi
                </a>
            </small>
        @endif
        @error('sertifikasi')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
</form>
@endsection
