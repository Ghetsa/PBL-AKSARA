{{-- /resources/views/user/show_ajax.blade.php --}}

<div class="modal-header bg-light">
    <h5 class="modal-title" id="myModalLabel">
        <i class="fas fa-user-circle me-2"></i>Detail User: {{ $user->nama }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body" style="max-height: 68vh; overflow-y: auto;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center mb-4">
                @php
                if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                    $avatar = asset('storage/' . $user->foto);
                } else {
                    // Avatar default berdasarkan role
                    switch ($user->role) {
                        case 'mahasiswa':
                            $avatar = asset('mantis/dist/assets/images/user/1.jpg');
                            break;
                        case 'admin':
                            $avatar = asset('mantis/dist/assets/images/user/2.jpg');
                            break;
                        case 'dosen':
                            $avatar = asset('mantis/dist/assets/images/user/3.jpg');
                            break;
                        default:
                            $avatar = asset('mantis/dist/assets/images/user/avatar-2.jpg');
                            break;
                    }
                }
                @endphp
                <img src="{{ $avatar }}"
                    alt="Foto Profil" class="img-fluid rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">

                <h4 class="mt-3 mb-1">{{ $user->nama }}</h4>
                <p class="text-muted">{{ ucfirst($user->role) }}</p>

                @if($user->status == 'aktif')
                    <span class="badge bg-success-soft text-success text-md">
                        <i class="fas fa-check-circle me-1"></i> Aktif
                    </span>
                @else
                    <span class="badge bg-danger-soft text-danger text-md">
                        <i class="fas fa-times-circle me-1"></i> Nonaktif
                    </span>
                @endif
            </div>

            <div class="col-12">
                <hr>
                <ul class="list-group list-group-flush">
                    {{-- <li class="list-group-item d-flex flex-column flex-row justify-content-between align-items-center px-0"> --}}
                    {{-- <li class="list-group-item d-flex flex-column flex-sm-row justify-content-between align-items-sm-center px-0"> --}}
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-lg col-6"><i class="fas fa-envelope me-2 text-primary"></i>Email</span>
                        <span class="text-lg text-end text-break col-6">{{ $user->email }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-lg col-6"><i class="fas fa-phone me-2 text-primary"></i>No. Telepon</span>
                        <span class="text-end text-lg col-6">{{ $user->no_telepon ?: '-' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-lg col-6"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Alamat</span>
                        <span class="text-end text-lg col-6">{{ $user->alamat ?: '-' }}</span>
                    </li>
                    @if($user->role == 'admin' && $user->admin)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-lg col-6"><i class="fas fa-id-card me-2 text-primary"></i>NIP Admin</span>
                            <span class="text-end text-lg col-6">{{ $user->admin->nip ?: '-' }}</span>
                        </li>
                    @elseif($user->role == 'dosen' && $user->dosen)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-lg col-6"><i class="fas fa-id-card me-2 text-primary"></i>NIP Dosen</span>
                            <span class="text-end text-lg col-6">{{ $user->dosen->nip ?: '-' }}</span>
                        </li>
                    @elseif($user->role == 'mahasiswa' && $user->mahasiswa)
                        <li li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-lg col-6"><i class="fas fa-id-card me-2 text-primary"></i>NIM</span>
                            <span class="text-end text-lg col-6">{{ $user->mahasiswa->nim ?: '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-lg col-6"><i class="fas fa-graduation-cap me-2 text-primary"></i>Program Studi</span>
                            <span class="text-end text-lg col-6">{{ $user->mahasiswa->prodi ? $user->mahasiswa->prodi->nama : '-' }}</span>
                        </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-lg col-6"><i class="fas fa-calendar-alt me-2 text-primary"></i>Periode semester</span>
                            <span class="text-end text-lg col-6">
                                @if($user->mahasiswa->periode)
                                    {{ $user->mahasiswa->periode->tahun_akademik }} / {{ $user->mahasiswa->periode->semester }}
                                @else
                                    -
                                @endif
                            </span>
                        </li>
                    @endif                
                </ul>

            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
</div>

{{-- CSS untuk efek soft-badge jika belum ada --}}
<style>
    .bg-success-soft {
        background-color: rgba(25, 135, 84, 0.15);
    }
    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.15);
    }
</style>