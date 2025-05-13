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
                                                    <img class="rounded-circle img-fluid wid-70" src="{{ $avatar }}" alt="Foto Profil {{ $user->nama }}">
                                                </div>
                                                <h5 class="mb-0 mt-2">{{ $user->nama }}</h5>
                                                <p class="text-muted text-sm">{{ ucfirst($user->role) }}</p>
                                                <hr class="my-3">
                                                {{-- Informasi kontak dasar bisa ditambahkan di sini jika ada, meniru MANTIS --}}
                                                <div class="d-flex align-items-center justify-content-start w-100 mb-2">
                                                    <i class="ti ti-mail me-2"></i>
                                                    <p class="mb-0 text-muted text-sm">{{ $user->email }}</p>
                                                </div>
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
                                    {{-- @if($user->role == 'dosen' || $user->role == 'mahasiswa')
                                    <div class="card mt-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5>Keahlian</h5>
                                            <div>
                                                <button class="btn btn-sm btn-icon btn-outline-primary me-1" onclick="openProfileModal('keahlian')"><i class="ti ti-plus"></i></button>
                                                <button class="btn btn-sm btn-icon btn-outline-secondary" onclick="openProfileModal()"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @forelse($user->keahlian as $k)
                                            <div class="mb-2">
                                                <p class="mb-0 fw-bold">{{ $k->keahlian_nama }}</p>
                                                @if($k->sertifikasi && $k->sertifikasi !== '-')
                                                    <small class="text-muted d-block">Sertifikasi: {{ $k->sertifikasi }}</small>
                                                @endif
                                            </div>
                                            @empty
                                                <p class="text-muted">Belum ada keahlian yang ditambahkan.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                    @endif

                                    @if($user->role == 'dosen' || $user->role == 'mahasiswa')
                                    <div class="card mt-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5>Pengalaman</h5>
                                            <div>
                                                <button class="btn btn-sm btn-icon btn-outline-primary me-1" onclick="openProfileModal('pengalaman')"><i class="ti ti-plus"></i></button>
                                                <button class="btn btn-sm btn-icon btn-outline-secondary" onclick="openProfileModal()"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @forelse($user->pengalaman as $p)
                                            <div class="mb-3 pb-3 border-bottom">
                                                <h6 class="mb-0">{{ $p->nama_pengalaman }}</h6>
                                                    @if($p->pengalaman_kategori)
                                                    <small>({{ $p->pengalaman_kategori }})</small>
                                                    @endif
                                            </div>
                                            @empty
                                                <p class="text-muted">Belum ada pengalaman yang ditambahkan.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                    @endif

                                    @if($user->role == 'dosen' || $user->role == 'mahasiswa')
                                    <div class="card mt-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5>Minat</h5>
                                            <div>
                                                <button class="btn btn-sm btn-icon btn-outline-primary me-1" onclick="openProfileModal('minat')"><i class="ti ti-plus"></i></button>
                                                <button class="btn btn-sm btn-icon btn-outline-secondary" onclick="openProfileModal()"><i class="ti ti-pencil"></i></button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @if($user->minat && $user->minat->count() > 0)
                                                @foreach($user->minat as $m)
                                                    <span class="badge bg-light text-dark me-1 mb-1 p-2">{{ $m->nama_minat }}</span>
                                                @endforeach
                                            @else
                                                <p class="text-muted">Belum ada minat yang ditambahkan.</p>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    <div class="mt-3 text-end">
                                        <button id="btnEditProfile" class="btn btn-primary">Ubah Profil</button>
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
        // Fungsi modalAction yang sudah ada
        function modalAction(url = '', section = null) {
            $('#updateProfileModal').html('<div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-body"><p class="text-center">Memuat form...</p></div></div></div>'); // Initial loading state
            $('#updateProfileModal').load(url, function (responseText, textStatus, req) {
                if (textStatus === "error") {
                    console.error("Gagal memuat konten modal: " + req.status + " " + req.statusText);
                    $('#updateProfileModal').html('<div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-body"><p class="text-danger text-center">Gagal memuat form. Silakan coba lagi.</p></div></div></div>');
                } else {
                    // Jika ada section yang dituju, coba aktifkan tab atau scroll ke elemen
                    if(section && $(this).find('#nav-' + section + '-tab').length) {
                        var tab = new bootstrap.Tab(document.getElementById('nav-' + section + '-tab'));
                        tab.show();
                    } else if (section && $(this).find('#section-' + section).length) {
                        // Scroll ke section jika bukan tab
                        var modalBody = $(this).find('.modal-body');
                        modalBody.scrollTop(0); // Reset scroll
                        var sectionElement = $(this).find('#section-' + section);
                        if(sectionElement.length) {
                            modalBody.animate({
                                scrollTop: sectionElement.offset().top - modalBody.offset().top + modalBody.scrollTop() - 20 // -20px offset
                            }, 500);
                        }
                    }
                }
                $('#updateProfileModal').modal('show');
            });
        }

        // Fungsi baru untuk trigger modal
        function openProfileModal(section = null) {
            var profileAjaxUrl = "{{ url('user/profile_ajax') }}";
            modalAction(profileAjaxUrl, section);
        }

        $(document).ready(function () {
            // Tombol "Ubah Profil" utama di header card kanan
            $('#btnEditProfile').click(function () {
                openProfileModal();
            });
        });
    </script>
@endpush --}}
                                    

{{-- @extends('layouts.template')

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Card Profil -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Data Profil</h3>
                </div>
                <div class="card-body">
                    @php
                        $role = Auth::user()->role;
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
                    } @endphp
                    <div class="text-center mb-3">
                        <img src="{{ $avatar }}" class="img-circle elevation-2" alt="Foto Profil" style="max-width:150px;">
                    </div>

                    <p><strong>Nama:</strong> {{ $user->nama }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>

                    @if($user->role == 'dosen')
                        <hr>
                        <p><strong>NIP:</strong> {{ $user->dosen->nip ?? '-' }}</p>
                        <p><strong>Gelar:</strong> {{ $user->dosen->gelar ?? '-' }}</p>
                        <p><strong>No. HP:</strong> {{ $user->dosen->no_hp ?? '-' }}</p>

                        <h5 class="mt-3">Keahlian:</h5>
                        <ul>
                            @forelse($user->keahlian as $k)
                                <li>{{ $k->keahlian_nama }} ({{ $k->sertifikasi }})</li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>

                        <h5 class="mt-3">Minat:</h5>
                        <ul>
                            @forelse($user->minat as $m)
                                <li>{{ $m->nama_minat }}</li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>

                        <h5 class="mt-3">Pengalaman:</h5>
                        <ul>
                            @forelse($user->pengalaman as $p)
                                <li>{{ $p->nama_pengalaman }} ({{ $p->tahun }})</li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>

                    @elseif($user->role == 'mahasiswa')
                        <hr>
                        <p><strong>NIM:</strong> {{ $user->mahasiswa->nim ?? '-' }}</p>
                        <p><strong>Program Studi:</strong> {{ $user->mahasiswa->prodi->nama ?? '-' }}</p>
                        <p><strong>Periode:</strong> {{ $user->mahasiswa->periode->tahun_akademik ?? '-' }}</p>

                        <h5 class="mt-3">Keahlian:</h5>
                        <ul>
                            @forelse($user->keahlian as $k)
                                <li>{{ $k->keahlian_nama }} ({{ $k->sertifikasi }})</li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>

                        <h5 class="mt-3">Minat:</h5>
                        <ul>
                            @forelse($user->minat as $m)
                                <li>{{ $m->minat }}</li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>

                        <h5 class="mt-3">Pengalaman:</h5>
                        <ul>
                            @forelse($user->pengalaman as $p)
                                <li>{{ $p->pengalaman_nama }} ({{ $p->pengalaman_kategori }})</li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>

                        <h5 class="mt-3">Prestasi:</h5>
                        <ul>
                            @forelse($user->mahasiswa->prestasi as $pres)
                                <li>{{ $pres->nama_prestasi }} - {{ $pres->tingkat }}</li>
                            @empty
                                <li>-</li>
                            @endforelse
                        </ul>

                    @elseif($user->role == 'admin')
                        <hr>
                        <p><strong>NIP:</strong> {{ $user->admin->nip ?? '-' }}</p>
                    @endif

                    <!-- Tombol untuk membuka modal edit profil -->
                    <button id="btnEditProfile" class="btn btn-warning mt-3">Ubah Profil</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal untuk Update Profile -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-hidden="true"></div>
@endsection

@push('js')
    <script>
        function modalAction(url = '') {
            $('#updateProfileModal').load(url, function () {
                $('#updateProfileModal').modal('show');
            });
        }

        $(document).ready(function () {
            // Saat tombol "Ubah Profil" diklik, muat form update secara AJAX ke dalam modal
            $('#btnEditProfile').click(function () {
                modalAction("{{ url('user/profile_ajax') }}"); // Sesuaikan dengan route-mu
            });
        });
    </script>
@endpush --}}