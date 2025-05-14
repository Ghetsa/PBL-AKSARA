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
                        <div class="tab-pane show active" id="profile-overview" role="tabpanel" aria-labelledby="profile-tab-1">
                            <div class="row">
                                <div class="col-lg-12 col-xxl-12">
                                    <div class="card">
                                        {{-- <div class="card-header">
                                            <h4>Detail Profil</h4>
                                        </div> --}}
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
                                                {{-- <h4 class="mb-0 text-lg mt-2">{{ $user->nama }}</h4>
                                                <p class="text-muted text-lg">{{ ucfirst($user->role) }}</p> --}}
                                                {{-- <hr class="my-3"> --}}
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

                                    {{-- Card untuk Keahlian (jika ada dan ingin dipisah seperti MANTIS) --}}
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

                                     {{-- Card untuk Minat --}}
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
                                                            <p class="mb-0 text-lg">{{ $m->nama }}</p>
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

                                    {{-- Card untuk Pengalaman --}}
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
                                                        <p class="mb-1">{{ $p->nama_pengalaman }}
                                                            @if($p->tahun && $p->tahun !== '-')
                                                            <span class="text-muted">({{ $p->tahun }})</span>
                                                            @endif
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

                                     {{-- Card untuk Prestasi (khusus mahasiswa) --}}
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

                                {{-- Kolom Kanan: Detail Informasi (seperti MANTIS) --}}
                                {{-- <div class="col-lg-8 col-xxl-9">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Detail Profil</h5>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0 pt-0">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted text-lg">Nama Lengkap</p>
                                                            <p class="mb-0 text-lg">{{ $user->nama }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted text-lg">Email</p>
                                                            <p class="mb-0 text-lg">{{ $user->email }}</p>
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
                                    </div> --}}
                                    <div class="mt-3 text-end">
                                        <button id="btnEditProfile" class="btn btn-primary btn-lg text-lg">Perbarui Profil</button>
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
                                                <p class="mb-0 text-lg fw-bold">{{ $k->keahlian_nama }}</p>
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
                                                <h6 class="mb-0 text-lg">{{ $p->nama_pengalaman }}</h6>
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