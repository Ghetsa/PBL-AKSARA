{{-- @extends('layouts.template')

@section('content') --}}
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">Edit user</h3>
            <div class="card-tools"></div>
        </div>
        <div class="card-body">
            @empty($data)
                <div class="alert alert-danger alert-dismissible">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!</h5>
                    Data yang Anda cari tidak ditemukan.
                </div>
                <a href="{{ url('user') }}" class="btn btn-sm btn-default mt-2">Kembali</a>
            @else
                <form method="POST" action="{{ url('/user/' . $data->user_id) }}" class="form
horizontal">
                    @csrf
                    {!! method_field('PUT') !!} <!-- tambahkan baris ini untuk proses edit yang butuh
                                                                                            method PUT -->
                    <div class="form-group row">
                        <label for="role" class="col-sm-2 col-form-label">Role</label>
                        <div class="col-sm-10">
                            <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">- Pilih Role -</option>
                                <option value="admin" {{ old('role', $data->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="mahasiswa" {{ old('role', $data->role) == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                                <option value="dosen" {{ old('role', $data->role) == 'dosen' ? 'selected' : '' }}>Dosen</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Nama</label>
                        <div class="col-11">
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="{{ old('nama', $data->nama) }}" required>
                            @error('nama')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Email</label>
                        <div class="col-11">
                            <input type="text" class="form-control" id="email" name="email"
                                value="{{ old('email', $data->email) }}" required>
                            @error('email')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="role" class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">- Pilih Status -</option>
                                <option value="aktif" {{ old('status', $data->status) == 'aktif' ? 'selected' : '' }}>aktif</option>
                                <option value="nonaktif" {{ old('status', $data->status) == 'nonaktif' ? 'selected' : '' }}>nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label">Password</label>
                        <div class="col-11">
                            <input type="password" class="form-control" id="password" name="password">
                            @error('password')
                                <small class="form-text text-danger">{{ $message }}</small>
                            @else
                                <small class="form-text text-muted">Abaikan (jangan diisi) jika tidak ingin
                                    mengganti password user.</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-1 control-label col-form-label"></label>
                        <div class="col-11">
                            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                            <a class="btn btn-sm btn-default ml-1" href="{{ url('user') }}">Kembali</a>
                        </div>
                    </div>
                </form>
            @endempty
        </div>
    </div>
{{-- @endsection --}}

{{-- @push('css')
@endpush
@push('js')
@endpush --}}
