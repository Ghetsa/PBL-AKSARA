{{-- resources/views/user/show_ajax.blade.php --}}

<div class="modal-header">
    <h5 class="modal-title" id="myModalLabel">Detail User: {{ $user->nama }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <th style="width: 30%;">ID User</th>
                <td>{{ $user->user_id }}</td>
            </tr>
            <tr>
                <th>Nama Lengkap</th>
                <td>{{ $user->nama }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $user->email }}</td>
            </tr>
            <tr>
                <th>No. Telepon</th>
                <td>{{ $user->no_telepon ?: '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ $user->alamat ?: '-' }}</td>
            </tr>
            <tr>
                <th>Role</th>
                <td>{{ ucfirst($user->role) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($user->status == 'aktif')
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </td>
            </tr>

            {{-- Informasi Spesifik Berdasarkan Role --}}
            @if($user->role == 'admin' && $user->admin)
                <tr>
                    <th>NIP Admin</th>
                    <td>{{ $user->admin->nip ?: '-' }}</td>
                </tr>
            @elseif($user->role == 'dosen' && $user->dosen)
                <tr>
                    <th>NIP Dosen</th>
                    <td>{{ $user->dosen->nip ?: '-' }}</td>
                </tr>
            @elseif($user->role == 'mahasiswa' && $user->mahasiswa)
                <tr>
                    <th>NIM</th>
                    <td>{{ $user->mahasiswa->nim ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Program Studi</th>
                    <td>{{ $user->mahasiswa->prodi ? $user->mahasiswa->prodi->nama : '-' }}</td>
                </tr>
                <tr>
                    <th>Periode Masuk</th>
                    <td>
                        @if($user->mahasiswa->periode)
                            {{ $user->mahasiswa->periode->tahun_akademik }} / {{ $user->mahasiswa->periode->semester }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div> {{-- Akhir dari modal-body --}}

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>