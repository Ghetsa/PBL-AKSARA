@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h4 class="card-title">Tambah User</h4>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('user.store') }}" class="form-horizontal">
                @csrf
                <div class="form-group row">
                    <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama"
                            name="nama" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="role" class="col-sm-2 col-form-label">Role</label>
                    <div class="col-sm-10">
                        <select class="form-control @error('role') is-invalid @enderror" id="role" name="role"
                            required>
                            <option value="">- Pilih Role -</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="form-nip" style="display: none;">
                    <div class="form-group row">
                        <label for="nip" class="col-sm-2 col-form-label">NIP</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nip" name="nip"
                                value="{{ old('nip') }}">
                        </div>
                    </div>
                </div>

                <div id="form-keahlian" style="display: none;">
                    <div class="form-group row">
                        <label for="bidang_keahlian" class="col-sm-2 col-form-label">Bidang Keahlian</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="bidang_keahlian" name="bidang_keahlian"
                                value="{{ old('bidang_keahlian') }}">
                        </div>
                    </div>
                </div>

                <div id="form-nim" style="display: none;">
                    <div class="form-group row">
                        <label for="nim" class="col-sm-2 col-form-label">NIM</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="nim" name="nim"
                                value="{{ old('nim') }}">
                        </div>
                    </div>
                </div>

                <div id="form-prodi_id" class="form-group row" style="display: none;">
                    <label for="prodi_id" class="col-sm-2 col-form-label">Prodi</label>
                    <div class="col-sm-10">
                        <select class="form-control @error('prodi_id') is-invalid @enderror" id="prodi_id" name="prodi_id"
                            required>
                            <option value="">- Pilih Prodi -</option>
                            <option value="1" {{ old('prodi_id') == '1' ? 'selected' : '' }}>Informatika
                            </option>
                            <option value="2" {{ old('prodi_id') == '2' ? 'selected' : '' }}>Sistem
                                Informasi</option>
                        </select>
                        @error('prodi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="form-periode_id" class="form-group row" style="display: none;">
                    <label for="periode_id" class="col-sm-2 col-form-label">Periode</label>
                    <div class="col-sm-10">
                        <select class="form-control @error('periode_id') is-invalid @enderror" id="periode_id"
                            name="periode_id" required>
                            <option value="">- Pilih Periode -</option>
                            <option value="1" {{ old('periode_id') == '1' ? 'selected' : '' }}>2024/2025
                            </option>
                            <option value="2" {{ old('periode_id') == '2' ? 'selected' : '' }}>2025/2026
                            </option>
                        </select>
                        @error('periode_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label for="status" class="col-sm-2 col-form-label">Status</label>
                    <div class="col-sm-10">
                        <select class="form-control @error('status') is-invalid @enderror" id="status" name="status"
                            required>
                            <option value="">- Pilih Status -</option>
                            <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a class="btn btn-secondary ml-2" href="{{ route('user.index') }}">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const formNip = document.getElementById('form-nip');
            const formKeahlian = document.getElementById('form-keahlian');
            const formNim = document.getElementById('form-nim');
            const formProdi_id = document.getElementById('form-prodi_id');
            const formPeriode = document.getElementById('form-periode_id');

            function toggleAdditionalForms() {
                const role = roleSelect.value;
                if (role === '') {
                    formNip.style.display = 'none';
                    formNim.style.display = 'none';
                    formKeahlian.style.display = 'none';
                    formProdi_id.style.display = 'none';
                    formPeriode.style.display = 'none';
                }

                if (role === 'admin') {
                    formNip.style.display = 'block';
                    formNim.style.display = 'none';
                    formKeahlian.style.display = 'none';
                    formProdi_id.style.display = 'none';
                    formPeriode.style.display = 'none';
                }
                if (role === 'dosen') {
                    formNip.style.display = 'block';
                    formNim.style.display = 'none';
                    formKeahlian.style.display = 'block';
                    formProdi_id.style.display = 'none';
                    formPeriode.style.display = 'none';
                }
                if (role === 'mahasiswa') {
                    formNip.style.display = 'none';
                    formKeahlian.style.display = 'none';
                    formNim.style.display = 'block';
                    formProdi_id.style.display = 'block';
                    formPeriode.style.display = 'block';
                }
            }

            roleSelect.addEventListener('change', toggleAdditionalForms);

            // Tampilkan saat reload form lama (misal gagal validasi)
            toggleAdditionalForms();
        });
    </script>
@endpush
