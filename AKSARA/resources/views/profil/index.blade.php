{{-- Versi 1 --}}
@extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-2">
                    <h3 class="card-title">Profil Pengguna</h3>
                </div>
                <div class="card-body">

                    <div class="tab-content">
                        {{-- Tab Pane Utama  --}}
                        <div class="tab-pane show active" id="profile-overview" role="tabpanel" aria-labelledby="profile-tab-1">
                            <div class="row">
                                <div class="col-lg-4 col-xxl-3">
                                    <div class="card">
                                        <div class="card-body position-relative">
                                            @php
                                                $role = Auth::user()->role;
                                                $user = Auth::user(); // Mengambil data user yang sedang login
                                                $avatar = '';
                                                switch ($role) {
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
                                                        $avatar = asset('mantis/dist/assets/images/user/avatar-2.jpg'); // fallback
                                                        break;
                                                }
                                            @endphp
                                            <div class="text-center mt-3">
                                                <div class="chat-avtar d-inline-flex mx-auto">
                                                    <img class="rounded-circle img-fluid wid-auto" src="{{ $avatar }}" alt="Foto Profil {{ $user->nama }}">
                                                </div>
                                                <h5 class="mb-0 mt-2">{{ $user->nama }}</h5>
                                                <p class="text-muted text-sm">{{ ucfirst($user->role) }}</p>
                                                <hr class="my-3">
                                                {{-- <div class="d-flex align-items-center justify-content-start w-100 mb-2">
                                                    <i class="ti ti-mail me-2"></i>
                                                    <p class="mb-0 text-muted text-sm">{{ $user->email }}</p>
                                                </div> --}}
                                                    @if($user->role == 'dosen' && isset($user->dosen->no_hp))
                                                    <div class="d-flex align-items-center justify-content-start w-100 mb-2">
                                                        <i class="ti ti-phone me-2"></i>
                                                        <p class="mb-0 text-muted text-sm">{{ $user->dosen->no_hp }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Card untuk Keahlian (jika ada dan ingin dipisah seperti MANTIS) --}}
                                    @if(($user->role == 'dosen' || $user->role == 'mahasiswa') && $user->keahlian && $user->keahlian->count() > 0)
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Keahlian</h5>
                                        </div>
                                        <div class="card-body">
                                            @foreach($user->keahlian as $k)
                                            <div class="row align-items-center mb-2">
                                                <div class="col-sm-12">
                                                    <p class="mb-0">{{ $k->keahlian_nama }}
                                                        @if($k->sertifikasi && $k->sertifikasi !== '-')
                                                            <small class="text-muted">({{ $k->sertifikasi }})</small>
                                                        @endif
                                                    </p>
                                                </div>
                                                {{-- Bisa ditambahkan progress bar jika ada persentase skill --}}
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif

                                </div>

                                {{-- Kolom Kanan: Detail Informasi (seperti MANTIS) --}}
                                <div class="col-lg-8 col-xxl-9">
                                    {{-- Card: Personal Details --}}
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Detail Personal</h5>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0 pt-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Nama Lengkap</p>
                                                            <p class="mb-0">{{ $user->nama }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Email</p>
                                                            <p class="mb-0">{{ $user->email }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item px-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Role</p>
                                                            <p class="mb-0">{{ ucfirst($user->role) }}</p>
                                                        </div>
                                                        @if($user->role == 'dosen')
                                                            <div class="col-md-6">
                                                                <p class="mb-1 text-muted">NIP</p>
                                                                <p class="mb-0">{{ $user->dosen->nip ?? '-' }}</p>
                                                            </div>
                                                        @elseif($user->role == 'mahasiswa')
                                                            <div class="col-md-6">
                                                                <p class="mb-1 text-muted">NIM</p>
                                                                <p class="mb-0">{{ $user->mahasiswa->nim ?? '-' }}</p>
                                                            </div>
                                                        @elseif($user->role == 'admin')
                                                            <div class="col-md-6">
                                                                <p class="mb-1 text-muted">NIP</p>
                                                                <p class="mb-0">{{ $user->admin->nip ?? '-' }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </li>
                                                @if($user->role == 'dosen')
                                                <li class="list-group-item px-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Gelar</p>
                                                            <p class="mb-0">{{ $user->dosen->gelar ?? '-' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">No. HP</p>
                                                            <p class="mb-0">{{ $user->dosen->no_hp ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                @elseif($user->role == 'mahasiswa')
                                                <li class="list-group-item px-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Program Studi</p>
                                                            <p class="mb-0">{{ $user->mahasiswa->prodi->nama ?? '-' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted">Periode</p>
                                                            <p class="mb-0">{{ $user->mahasiswa->periode->tahun_akademik ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>

                                    {{-- Card untuk Minat --}}
                                    @if(($user->role == 'dosen' || $user->role == 'mahasiswa') && $user->minat && $user->minat->count() > 0)
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Minat</h5>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                @foreach($user->minat as $m)
                                                    <li class="list-group-item px-0 pt-0">
                                                        <p class="mb-0">{{ $m->nama_minat }}</p>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Card untuk Pengalaman --}}
                                    @if(($user->role == 'dosen' || $user->role == 'mahasiswa') && $user->pengalaman && $user->pengalaman->count() > 0)
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Pengalaman</h5>
                                        </div>
                                        <div class="card-body">
                                             <ul class="list-group list-group-flush">
                                                @foreach($user->pengalaman as $p)
                                                <li class="list-group-item px-0 pt-0">
                                                    <p class="mb-1">{{ $p->nama_pengalaman }}
                                                        @if($p->tahun && $p->tahun !== '-')
                                                        <span class="text-muted">({{ $p->tahun }})</span>
                                                        @endif
                                                    </p>
                                                    {{-- Deskripsi pengalaman bisa ditambahkan di sini jika ada --}}
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @endif

                                     {{-- Card untuk Prestasi (khusus mahasiswa) --}}
                                     @if($user->role == 'mahasiswa' && $user->mahasiswa && $user->mahasiswa->prestasi && $user->mahasiswa->prestasi->count() > 0)
                                     <div class="card">
                                         <div class="card-header">
                                             <h5>Prestasi</h5>
                                         </div>
                                         <div class="card-body">
                                             <ul class="list-group list-group-flush">
                                                 @foreach($user->mahasiswa->prestasi as $pres)
                                                 <li class="list-group-item px-0 pt-0">
                                                     <p class="mb-0">{{ $pres->nama_prestasi }} - <span class="text-muted">{{ $pres->tingkat }}</span></p>
                                                 </li>
                                                 @endforeach
                                             </ul>
                                         </div>
                                     </div>
                                     @endif

                                    <div class="mt-3 text-end">
                                        <button id="btnEditProfile" class="btn btn-primary">Perbarui Profil</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Jika ada tab lain, tambahkan <div class="tab-pane" id="profile-details" ...> di sini --}}
                    </div>
                </div>
            </div>
        </div>
        </div>
    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
        {{-- Konten modal akan dimuat oleh AJAX --}}
    </div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            // Pastikan modal di-reset sebelum memuat konten baru
            $('#updateProfileModal').html(''); // Mengosongkan konten modal sebelumnya
            $('#updateProfileModal').load(url, function (responseText, textStatus, req) {
                if (textStatus === "error") {
                    // Tangani error jika gagal memuat konten modal
                    console.error("Gagal memuat konten modal: " + req.status + " " + req.statusText);
                    // Anda bisa menampilkan pesan error di modal atau dengan cara lain
                    $('#updateProfileModal').html('<div class="modal-dialog"><div class="modal-content"><div class="modal-body"><p>Gagal memuat form. Silakan coba lagi.</p></div></div></div>');
                }
                $('#updateProfileModal').modal('show');
            });
        }

        $(document).ready(function () {
            // Saat tombol "Ubah Profil" diklik, muat form update secara AJAX ke dalam modal
            $('#btnEditProfile').click(function () {
                var profileAjaxUrl = "{{ url('user/profile_ajax') }}"; // Pastikan route ini benar
                modalAction(profileAjaxUrl);
            });
        });
    </script>
@endpush

{{-- Versi 2 --}}
{{-- @extends('layouts.template')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header pb-2">
                    <h3 class="card-title">Profil Pengguna</h3>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane show active" id="profile-overview" role="tabpanel" aria-labelledby="profile-tab-1">
                            <div class="row">
                                <div class="col-lg-12 col-xxl-12">
                                    <div class="card">
                                        <!-- <div class="card-header">
                                            <h4>Detail Profil</h4>
                                        </div> -->
                                        <div class="card-body position-relative">
                                            @php
                                                $role = Auth::user()->role;
                                                $user = Auth::user(); // Mengambil data user yang sedang login
                                                $avatar = '';
                                                switch ($role) {
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
                                                        $avatar = asset('mantis/dist/assets/images/user/avatar-2.jpg'); // fallback
                                                        break;
                                                }
                                            @endphp
                                            <div class="text-center mt-3">
                                                <div class="chat-avtar d-inline-flex mx-auto">
                                                    <img class="rounded-circle img-fluid wid-auto" src="{{ $avatar }}" alt="Foto Profil {{ $user->nama }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0 pt-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted text-lg">Nama Lengkap</p>
                                                            <p class="mb-0 text-lg text-lg">{{ $user->nama }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted text-lg">Email</p>
                                                            <p class="mb-0 text-lg text-lg">{{ $user->email }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="list-group-item px-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted text-lg">Role</p>
                                                            <p class="mb-0 text-lg">{{ ucfirst($user->role) }}</p>
                                                        </div>
                                                        @if($user->role == 'dosen')
                                                            <div class="col-md-6">
                                                                <p class="mb-1 text-muted text-lg">NIP</p>
                                                                <p class="mb-0 text-lg">{{ $user->dosen->nip ?? '-' }}</p>
                                                            </div>
                                                        @elseif($user->role == 'mahasiswa')
                                                            <div class="col-md-6">
                                                                <p class="mb-1 text-muted text-lg">NIM</p>
                                                                <p class="mb-0 text-lg">{{ $user->mahasiswa->nim ?? '-' }}</p>
                                                            </div>
                                                        @elseif($user->role == 'admin')
                                                            <div class="col-md-6">
                                                                <p class="mb-1 text-muted text-lg">NIP</p>
                                                                <p class="mb-0 text-lg">{{ $user->admin->nip ?? '-' }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </li>
                                                @if($user->role == 'mahasiswa')
                                                <li class="list-group-item px-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted text-lg">Program Studi</p>
                                                            <p class="mb-0 text-lg">{{ $user->mahasiswa->prodi->nama ?? '-' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted text-lg">Periode</p>
                                                            <p class="mb-0 text-lg">{{ $user->mahasiswa->periode->tahun_akademik ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>

                                    @if(($user->role == 'dosen' || $user->role == 'mahasiswa') && $user->keahlian)
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Keahlian</h4>
                                        </div>
                                        <div class="card-body">
                                            @if(($user->keahlian->count() > 0))
                                                @foreach($user->keahlian as $k)
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-sm-12">
                                                        <p class="mb-0 text-lg">{{ $k->keahlian_nama }}
                                                            @if($k->sertifikasi && $k->sertifikasi !== '-')
                                                                <small class="text-muted text-lg">({{ $k->sertifikasi }})</small>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                @endforeach
                                            @else
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-sm-12">
                                                        <p class="mb-0 text-lg">Anda belum menambahkan keahlian</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    @if(($user->role == 'dosen' || $user->role == 'mahasiswa') && $user->minat)
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Minat</h4>
                                        </div>
                                        <div class="card-body">
                                            @if(($user->minat->count() > 0))
                                                <ul class="list-group list-group-flush">
                                                    @foreach($user->minat as $m)
                                                        <li class="list-group-item px-0 pt-0">
                                                            <p class="mb-0 text-lg">{{ $m->minat }}</p>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-sm-12">
                                                        <p class="mb-0 text-lg">Anda belum menambahkan minat</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    @if(($user->role == 'mahasiswa') && $user->pengalaman)
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Pengalaman</h4>
                                        </div>
                                        <div class="card-body">
                                            @if(($user->pengalaman->count() > 0))
                                                <ul class="list-group list-group-flush">
                                                    @foreach($user->pengalaman as $p)
                                                    <li class="list-group-item px-0 pt-0">
                                                        <p class="mb-1">{{ $p->pengalaman_nama }}
                                                            <span class="text-muted">({{ $p->pengalaman_kategori }})</span>
                                                        </p>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-sm-12">
                                                        <p class="mb-0 text-lg">Anda belum menambahkan pengalaman</p>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                     @if($user->role == 'mahasiswa' && $user->mahasiswa && $user->mahasiswa->prestasi)
                                     <div class="card">
                                         <div class="card-header">
                                             <h4>Prestasi</h4>
                                         </div>
                                         <div class="card-body">
                                              @if($user->mahasiswa->prestasi->count() > 0)
                                                <ul class="list-group list-group-flush">
                                                    @foreach($user->mahasiswa->prestasi as $pres)
                                                    <li class="list-group-item px-0 pt-0">
                                                        <p class="mb-0 text-lg">{{ $pres->nama_prestasi }} - <span class="text-muted">{{ $pres->tingkat }}</span></p>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <div class="row align-items-center mb-2">
                                                    <div class="col-sm-12">
                                                        <p class="mb-0 text-lg">Anda belum memiliki prestasi</p>
                                                    </div>
                                                </div>
                                            @endif
                                         </div>
                                     </div>
                                     @endif

                                </div>
                                    <div class="mt-3 text-end">
                                        <button id="btnEditProfile" class="btn btn-primary btn-lg text-lg">Perbarui Profil</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    </div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            // Pastikan modal di-reset sebelum memuat konten baru
            $('#updateProfileModal').html(''); // Mengosongkan konten modal sebelumnya
            $('#updateProfileModal').load(url, function (responseText, textStatus, req) {
                if (textStatus === "error") {
                    // Tangani error jika gagal memuat konten modal
                    console.error("Gagal memuat konten modal: " + req.status + " " + req.statusText);
                    // Anda bisa menampilkan pesan error di modal atau dengan cara lain
                    $('#updateProfileModal').html('<div class="modal-dialog"><div class="modal-content"><div class="modal-body"><p>Gagal memuat form. Silakan coba lagi.</p></div></div></div>');
                }
                $('#updateProfileModal').modal('show');
            });
        }

        $(document).ready(function () {
            // Saat tombol "Ubah Profil" diklik, muat form update secara AJAX ke dalam modal
            $('#btnEditProfile').click(function () {
                var profileAjaxUrl = "{{ url('user/profile_ajax') }}"; // Pastikan route ini benar
                modalAction(profileAjaxUrl);
            });
        });
    </script>
@endpush --}}
