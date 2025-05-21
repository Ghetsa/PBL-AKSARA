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
                        <div class="tab-pane show active" id="profile-overview" role="tabpanel"
                            aria-labelledby="profile-tab-1">
                            <div class="row">
                                <div class="col-lg-4 col-xxl-3">
                                    {{-- Card Avatar dan Info Singkat --}}
                                    <div class="card">
                                        <div class="card-body position-relative">
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
                                            <div class="text-center mt-3">
                                                <div class="chat-avtar d-inline-flex mx-auto">
                                                    <img src="{{ $avatar }}" alt="Foto Profil"
                                                        class="rounded-circle img-fluid"
                                                        style="width: 130px; height: 130px; object-fit: cover;">
                                                </div>
                                                <h4 class="mb-0 mt-2">{{ $user->nama }}</h4>
                                                <p class="text-muted text-md">{{ ucfirst($user->role) }}</p>
                                                <hr class="my-3">
                                            </div>
                                        </div>
                                    </div>

                                    @if($user->role == 'dosen' || $user->role == 'mahasiswa')
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h4 class="mb-0">Keahlian</h4>
                                            </div>
                                            @if($user->keahlianUser && $user->keahlianUser->count() > 0)
                                                <div class="card-body">
                                                    @foreach($user->keahlianUser as $keahlian_item)
                                                        <div class="mb-3 pb-2 @if(!$loop->last) border-bottom @endif">
                                                            <h6 class="mb-1">{{ $keahlian_item->bidang->bidang_nama ?? '-' }}</h6>

                                                            @if($keahlian_item->sertifikasi)
                                                                @php
                                                                    $sertifikasiExists = Storage::disk('public')->exists($keahlian_item->sertifikasi);
                                                                @endphp
                                                                @if($sertifikasiExists)
                                                                    <small class="d-block">
                                                                        Sertifikasi:
                                                                        <a href="{{ Storage::url($keahlian_item->sertifikasi) }}"
                                                                            target="_blank" class="btn-link">Lihat File</a>
                                                                        (
                                                                        @if($keahlian_item->status_verifikasi == 'disetujui')
                                                                            <span class="badge bg-success"><i
                                                                                    class="fas fa-check-circle me-1"></i>Disetujui</span>
                                                                        @elseif($keahlian_item->status_verifikasi == 'ditolak')
                                                                            <span class="badge bg-danger"><i
                                                                                    class="fas fa-times-circle me-1"></i>Ditolak</span>
                                                                            @if($keahlian_item->catatan_verifikasi)
                                                                                <em class="d-block text-danger small fst-italic">Catatan:
                                                                                    {{ $keahlian_item->catatan_verifikasi }}</em>
                                                                            @endif
                                                                        @else
                                                                            <span class="badge bg-warning text-dark"><i
                                                                                    class="fas fa-hourglass-half me-1"></i>Pending</span>
                                                                        @endif
                                                                        )
                                                                    </small>
                                                                @else
                                                                    <small class="text-danger d-block">File sertifikat tidak ditemukan.</small>
                                                                @endif
                                                            @else
                                                                <small class="text-muted d-block">Belum ada sertifikat diunggah untuk
                                                                    keahlian ini.</small>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="card-body">
                                                    <p class="mb-0 text-muted">Belum ada keahlian yang ditambahkan.</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif


                                    {{-- Card untuk Keahlian --}}
                                    {{-- @if($user->role == 'dosen' || $user->role == 'mahasiswa')
                                    <div class="card mt-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4>Keahlian</h4>
                                        </div>
                                        @if($user->keahlian && $user->keahlian->count() > 0)
                                        <div class="card-body">
                                            @foreach($user->keahlian as $k)
                                            <div class="mb-2 pb-2 @if(!$loop->last) border-bottom @endif">
                                                <p class="mb-0 text-lg fw-bold">{{ $k->keahlian_nama }}</p>
                                                @if($k->sertifikasi && Storage::disk('public')->exists($k->sertifikasi))
                                                <small class="text-muted d-block">
                                                    Sertifikasi: <a href="{{ Storage::url($k->sertifikasi) }}"
                                                        target="_blank" class="btn btn-link btn-sm p-0">Lihat File</a>
                                                </small>
                                                @elseif($k->sertifikasi)
                                                <small class="text-info d-block">Sertifikasi: {{ $k->sertifikasi }}
                                                    (Catatan)</small>
                                                @else
                                                <!-- <small class="text-muted d-block">Sertifikasi: Tidak ada.</small> -->
                                                @endif
                                            </div>
                                            @endforeach
                                        </div>
                                        @else
                                        <div class="card-body">
                                            <p class="mb-0 text-muted">Belum ada keahlian yang ditambahkan.</p>
                                        </div>
                                        @endif
                                    </div>
                                    @endif --}}

                                </div>

                                <div class="col-lg-8 col-xxl-9">
                                    {{-- Card: Personal Details --}}
                                    <div class="card">
                                        <div class="card-header">
                                            <h4>Detail Personal</h4>
                                        </div>
                                        <div class="card-body">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0 pt-0">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-2 mb-md-0">
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
                                                        <div class="col-md-6 mb-2 mb-md-0">
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
                                                @if($user->role == 'mahasiswa' && $user->mahasiswa)
                                                    <li class="list-group-item px-0">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-2 mb-md-0">
                                                                <p class="mb-1 text-muted text-lg">Program Studi</p>
                                                                <p class="mb-0 text-lg">
                                                                    {{ $user->mahasiswa->prodi->nama ?? '-' }}
                                                                </p>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <p class="mb-1 text-muted text-lg">Periode</p>
                                                                <p class="mb-0 text-lg">
                                                                    {{ $user->mahasiswa->periode->tahun_akademik ?? '-' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>

                                    {{-- Card untuk Minat --}}
                                    @if($user->role == 'dosen' || $user->role == 'mahasiswa')
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h4 class="mb-0">Minat</h4>
                                            </div>
                                            @if($user->minatUser && $user->minatUser->count() > 0)
                                                <div class="card-body">
                                                    @foreach($user->minatUser as $minat_item)
                                                        <span class="badge bg-light text-dark me-1 mb-1 p-2 fs-6 border">
                                                            {{ $minat_item->bidang->bidang_nama ?? '-' }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="card-body">
                                                    <p class="mb-0 text-muted">Belum ada minat yang ditambahkan.</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- @if($user->role == 'dosen' || $user->role == 'mahasiswa')
                                    <div class="card mt-3">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4>Minat</h4>
                                        </div>
                                        @if($user->minat && $user->minat->count() > 0)
                                        <div class="card-body">
                                            @foreach($user->minat as $m)
                                            <span class="badge bg-light text-dark me-1 mb-1 p-2 fs-6">{{ $m->minat_nama ??
                                                $m->minat }}</span>
                                            @endforeach
                                        </div>
                                        @else
                                        <div class="card-body">
                                            <p class="mb-0 text-muted">Belum ada minat yang ditambahkan.</p>
                                        </div>
                                        @endif
                                    </div>
                                    @endif --}}

                                    {{-- Card untuk Pengalaman --}}
                                    @if($user->role == 'mahasiswa' || $user->role == 'dosen') {{-- Sesuaikan jika dosen juga
                                        menampilkan ini --}}
                                        <div class="card mt-3">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h4>Pengalaman</h4>
                                                {{-- Tombol Edit Pengalaman Dihilangkan (jika diedit via modal utama) --}}
                                            </div>
                                            @if($user->pengalaman && $user->pengalaman->count() > 0)
                                                <div class="card-body">
                                                    <ul class="list-group list-group-flush">
                                                        @foreach($user->pengalaman as $p)
                                                            <li
                                                                class="list-group-item px-0 @if(!$loop->first) pt-3 @else pt-0 @endif @if(!$loop->last) pb-3 @else pb-0 @endif">
                                                                <h6 class="mb-0">{{ $p->pengalaman_nama }}</h6>
                                                                @if($p->pengalaman_kategori)
                                                                    <p class="mb-0 text-sm text-muted">{{ $p->pengalaman_kategori }}</p>
                                                                @endif
                                                                {{-- Tambahkan detail lain jika ada (misal deskripsi, tahun) --}}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <div class="card-body">
                                                    <p class="mb-0 text-muted">Belum ada pengalaman yang ditambahkan.</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Card untuk Prestasi (khusus mahasiswa) --}}
                                    @if($user->role == 'mahasiswa' && $user->mahasiswa && $user->mahasiswa->prestasi && $user->mahasiswa->prestasi->count() > 0)
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h4>Prestasi</h4>
                                                {{-- Tombol edit prestasi mungkin ada di halaman prestasi sendiri --}}
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    @foreach($user->mahasiswa->prestasi as $index => $pres)
                                                        @if($index < 3) {{-- Tampilkan maksimal 3 prestasi di profil, sisanya di
                                                            halaman prestasi --}}
                                                            <li
                                                                class="list-group-item px-0 @if(!$loop->first) pt-3 @else pt-0 @endif @if(!$loop->last) pb-3 @else pb-0 @endif">
                                                                <p class="mb-0 text-lg">{{ $pres->nama_prestasi }}
                                                                    <span class="text-muted">({{ $pres->tingkat }} -
                                                                        {{ $pres->tahun }})</span>
                                                                </p>
                                                                @if($pres->file_bukti && Storage::disk('public')->exists($pres->file_bukti))
                                                                    <a href="{{ Storage::url($pres->file_bukti) }}" target="_blank"
                                                                        class="btn-link text-sm">Lihat Bukti</a>
                                                                @endif
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                    @if($user->mahasiswa->prestasi->count() > 3)
                                                        <li class="list-group-item px-0 pt-3 pb-0 text-center">
                                                            <a href="{{ route('prestasi.mahasiswa.index') }}">Lihat semua
                                                                prestasi...</a> {{-- Asumsi route ke halaman list prestasi mahasiswa
                                                            --}}
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-4 text-end">
                                        <button id="btnEditProfile" class="btn btn-primary">
                                            <i class="ti ti-pencil me-1"></i> Perbarui Profil
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk Update Profile (tetap sama) --}}
    <div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel"
        aria-hidden="true">
    </div>
@endsection

@push('js')
    <script>
        function openProfileModal(section = null) {
            var profileAjaxUrl = "{{ url('user/profile_ajax') }}";
            $('#updateProfileModal').html('<div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content"><div class="modal-body text-center py-5"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="visually-hidden">Loading...</span></div><p class="mt-3 fs-5">Memuat form edit profil...</p></div></div></div>');
            $('#updateProfileModal').modal('show');

            $('#updateProfileModal').load(profileAjaxUrl, function (responseText, textStatus, req) {
                if (textStatus === "error") {
                    console.error("Gagal memuat konten modal: " + req.status + " " + req.statusText);
                    $('#updateProfileModal .modal-content').html(
                        '<div class="modal-header"><h5 class="modal-title">Error</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>' +
                        '<div class="modal-body text-center py-5"><p class="text-danger fs-5">Gagal memuat form edit profil.</p><p class="text-muted">Silakan coba lagi nanti atau hubungi administrator.</p></div>' +
                        '<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div>'
                    );
                } else {
                    if (section && $('#updateProfileModal').find('#section-' + section).length) {
                        var modalBody = $('#updateProfileModal').find('.modal-body');
                        var sectionElement = $('#updateProfileModal').find('#section-' + section);
                        if (sectionElement.length) {
                            // Beri sedikit waktu agar modal sepenuhnya render sebelum scroll
                            setTimeout(function () {
                                modalBody.animate({
                                    scrollTop: sectionElement.offset().top - modalBody.offset().top + modalBody.scrollTop() - 20
                                }, 500);
                                sectionElement.addClass('highlight-section');
                                setTimeout(() => sectionElement.removeClass('highlight-section'), 2500);
                            }, 200);
                        }
                    }
                }
            });
        }

        $(document).ready(function () {
            $('#btnEditProfile').click(function () {
                openProfileModal(); // Tombol utama tetap membuka modal ke bagian atas
            });
        });
    </script>
@endpush

@push('css')
    <style>
        .highlight-section {
            animation: highlightAnimation 2.5s ease-out;
            border-left: 3px solid #0d6efd;
            /* Contoh highlight dengan border biru */
            padding-left: 10px;
            /* Beri sedikit padding agar border terlihat */
        }

        @keyframes highlightAnimation {
            0% {
                background-color: rgba(13, 110, 253, 0.1);
            }

            /* Biru muda transparan */
            70% {
                background-color: rgba(13, 110, 253, 0.1);
            }

            100% {
                background-color: transparent;
            }
        }

        .wid-auto {
            /* Untuk avatar agar tidak pecah jika ukuran asli kecil */
            max-width: 100px;
            /* Sesuaikan dengan ukuran yang Anda inginkan */
            height: auto;
        }

        .text-lg {
            /* Ukuran font sedikit lebih besar untuk item list */
            font-size: 1.05rem;
            /* Sesuaikan */
        }
    </style>
@endpush
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
                    <div class="tab-pane show active" id="profile-overview" role="tabpanel"
                        aria-labelledby="profile-tab-1">
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
                                                <img class="rounded-circle img-fluid wid-auto" src="{{ $avatar }}"
                                                    alt="Foto Profil {{ $user->nama }}">
                                            </div>
                                            <h4 class="mb-0 mt-2">{{ $user->nama }}</h4>
                                            <p class="text-muted text-md">{{ ucfirst($user->role) }}</p>
                                            <hr class="my-3">
                                            @if($user->role == 'dosen' && isset($user->dosen->no_hp))
                                            <div class="d-flex align-items-center justify-content-start w-100 mb-2">
                                                <i class="ti ti-phone me-2"></i>
                                                <p class="mb-0 text-muted text-sm">{{ $user->dosen->no_hp }}</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if(($user->role == 'dosen' || $user->role == 'mahasiswa'))
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Keahlian</h4>
                                    </div>
                                    @if(($user->keahlian->count() > 0))
                                    <div class="card-body">
                                        @foreach($user->keahlian as $k)
                                        <div class="row align-items-center mb-2">
                                            <div class="col-sm-12">
                                                <p class="mb-0 text-lg">{{ $k->keahlian_nama }}
                                                    @if($k->sertifikasi && $k->sertifikasi !== '-')
                                                    <small class="text-muted">({{ $k->sertifikasi }})</small>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="row align-items-center mb-2">
                                        <div class="col-sm-12">
                                            <p class="mb-0 text-lg">Anda belum menambahkan keahlian</p>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                            </div>

                            <div class="col-lg-8 col-xxl-9">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Detail Personal</h4>
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
                                                        <p class="mb-0 text-lg">{{ $user->mahasiswa->prodi->nama ?? '-'
                                                            }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1 text-muted text-lg">Periode</p>
                                                        <p class="mb-0 text-lg">{{
                                                            $user->mahasiswa->periode->tahun_akademik ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>

                                @if(($user->role == 'dosen' || $user->role == 'mahasiswa'))
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Minat</h4>
                                    </div>
                                    <div class="card-body">
                                        @if(($user->minat->count() > 0))
                                        <ul class="list-group list-group-flush">
                                            @foreach($user->minat as $m)
                                            <li class="list-group-item px-0 pt-0">
                                                <p class="mb-0 text-lg">{{ $m->nama_minat }}</p>
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

                                @if(($user->role == 'mahasiswa'))
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Pengalaman</h4>
                                    </div>
                                    <div class="card-body">
                                        @if(($user->pengalaman->count() > 0))
                                        <ul class="list-group list-group-flush">
                                            @foreach($user->pengalaman as $p)
                                            <li class="list-group-item px-0 pt-0">
                                                <p class="mb-1 text-lg" text-lg>{{ $p->pengalaman_nama }}
                                                    <span class="text-muted text-lg">({{ $p->pengalaman_kategori
                                                        }})</span>
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
                                                <p class="mb-0 text-lg">{{ $pres->nama_prestasi }} - <span
                                                        class="text-muted">{{ $pres->tingkat }}</span></p>
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

                                <div class="mt-3 text-end">
                                    <button id="btnEditProfile" class="btn btn-primary">Perbarui Profil</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel"
    aria-hidden="true">
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
                    <div class="tab-pane show active" id="profile-overview" role="tabpanel"
                        aria-labelledby="profile-tab-1">
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
                                                <img class="rounded-circle img-fluid wid-auto" src="{{ $avatar }}"
                                                    alt="Foto Profil {{ $user->nama }}">
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
                                                        <p class="mb-0 text-lg">{{ $user->mahasiswa->prodi->nama ?? '-'
                                                            }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1 text-muted text-lg">Periode</p>
                                                        <p class="mb-0 text-lg">{{
                                                            $user->mahasiswa->periode->tahun_akademik ?? '-' }}</p>
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
                                                    <span class="text-muted">({{ $p->pengalaman_pengalaman_kategori
                                                        }})</span>
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
                                                <p class="mb-0 text-lg">{{ $pres->nama_prestasi }} - <span
                                                        class="text-muted">{{ $pres->tingkat }}</span></p>
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
                                <button id="btnEditProfile" class="btn btn-primary btn-lg text-lg">Perbarui
                                    Profil</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="modal fade" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel"
    aria-hidden="true">
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