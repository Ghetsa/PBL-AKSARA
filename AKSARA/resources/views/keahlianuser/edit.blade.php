@extends('layouts.template')

@section('content')
<h4>Edit Keahlian</h4>

<form action="{{ route('mahasiswa.keahlianuser.update', $keahlianUser->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Keahlian</label>
        <select name="keahlian_id" class="form-control" required>
            <option value="">-- Pilih Keahlian --</option>
            @foreach ($keahlians as $k)
                <option value="{{ $k->keahlian_id }}" {{ $keahlianUser->keahlian_id == $k->keahlian_id ? 'selected' : '' }}>
                    {{ $k->keahlian_nama ?? 'Unknown' }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Sertifikasi (optional)</label>
        <input type="text" name="sertifikasi" class="form-control" maxlength="255" value="{{ old('sertifikasi', $keahlianUser->sertifikasi) }}" />
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
</form>
@endsection
