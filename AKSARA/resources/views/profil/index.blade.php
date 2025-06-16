{{-- profil/index.blade.php --}}
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
                                                <div class="d-inline-flex align-items-center justify-content-between w-100 mb-3">
                                                    <i class="ti ti-mail"></i>
                                                    <p class="mb-0">{{ ucfirst($user->email) }}</p>
                                                </div>
                                                <div class="d-inline-flex align-items-center justify-content-between w-100 mb-3">
                                                    <i class="ti ti-phone"></i>
                                                    <p class="mb-0">{{ ($user->no_telepon) }}</p>
                                                </div>
                                                <div class="d-inline-flex align-items-center justify-content-between w-100 mb-3">
                                                    <i class="ti ti-map-pin"></i>
                                                    <p class="mb-0">{{ Str::limit($user->alamat, 25) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($user->role == 'mahasiswa')
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h4 class="mb-0">Keahlian</h4>
                                            </div>
                                            @if($user->keahlianUser && $user->keahlianUser->count() > 0)
                                                <div class="card-body">
                                                    @foreach($user->keahlianUser as $keahlian_item)
                                                        <div class="mb-3 pb-2 @if(!$loop->last) border-bottom @endif">
                                                            {{-- <h6 class="mb-1"><i class="ti ti-certificate me-2 text-info"></i>{{ $keahlian_item->bidang->bidang_nama ?? '-' }}</h6> --}}
                                                            <h6 class="mb-1"><i class="fas fa-medal me-2 text-info"></i>{{ $keahlian_item->bidang->bidang_nama ?? '-' }}</h6>

                                                            @if($keahlian_item->sertifikasi)
                                                                @php
                                                                    $sertifikasiExists = Storage::disk('public')->exists($keahlian_item->sertifikasi);
                                                                @endphp
                                                                @if($sertifikasiExists)
                                                                    <small class="d-block">
                                                                        Sertifikasi:
                                                                        <a href="{{ asset('storage/' . $keahlian_item->sertifikasi) }}"
                                                                            target="_blank" class="btn-link">Lihat File</a>
                                                                        (
                                                                        @if($keahlian_item->status_verifikasi == 'disetujui')
                                                                            <span class="badge bg-light-success"><i
                                                                                    class="fas fa-check-circle me-1"></i>Disetujui</span>
                                                                        @elseif($keahlian_item->status_verifikasi == 'ditolak')
                                                                            <span class="badge bg-light-danger"><i
                                                                                    class="fas fa-times-circle me-1"></i>Ditolak</span>
                                                                            @if($keahlian_item->catatan_verifikasi)
                                                                                <em class="d-block text-danger small fst-italic">Catatan:
                                                                                    {{ $keahlian_item->catatan_verifikasi }}</em>
                                                                            @endif
                                                                        @else
                                                                            <span class="badge bg-light-warning"><i
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
                                                            <p class="mb-1 text-muted text-lg">Alamat</p>
                                                            {{-- <p class="mb-0 text-lg">{{ Str::limit($user->alamat, 10) }}</p> --}}
                                                            <p class="mb-0 text-lg">{{ $user->alamat ?? '-' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="mb-1 text-muted text-lg">Nomor Telepon</p>
                                                            <p class="mb-0 text-lg">{{ $user->no_telepon ?? '-' }}</p>
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

                                    {{-- Card untuk Pengalaman --}}
                                    @if($user->role == 'mahasiswa' || $user->role == 'dosen') 
                                        <div class="card mt-3">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h4>Pengalaman</h4>
                                            </div>
                                            @if($user->pengalaman && $user->pengalaman->count() > 0)
                                                <div class="card-body">
                                                    <ul class="list-group list-group-flush">
                                                        @foreach($user->pengalaman as $p)
                                                            <li
                                                                class="list-group-item px-0 @if(!$loop->first) pt-3 @else pt-0 @endif @if(!$loop->last) pb-3 @else pb-0 @endif">
                                                                <h6 class="mb-0"><i class="fas fa-briefcase me-2 text-primary"></i>{{ $p->pengalaman_nama }}</h6>
                                                                @if($p->pengalaman_kategori)
                                                                    <p class="mb-0 text-sm text-muted">{{ $p->pengalaman_kategori }}</p>
                                                                @endif
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
                                    @if($user->role == 'mahasiswa' && $user->mahasiswa && $user->mahasiswa->prestasi)
                                        @php
                                            $prestasiDisetujui = $user->mahasiswa->prestasi->where('status_verifikasi', 'disetujui');
                                            $prestasiDisetujuiTerbatas = $prestasiDisetujui->take(10); 
                                        @endphp
                                        <div class="card mt-3">
                                            <div class="card-header">
                                                <h4>Prestasi</h4>
                                            </div>
                                            @if($user->mahasiswa->prestasi->count() > 0)
                                                @if($prestasiDisetujuiTerbatas->count() > 0)
                                                    <div class="card-body">
                                                        <ul class="list-group list-group-flush">
                                                            @foreach($prestasiDisetujuiTerbatas as $pres)
                                                                <li
                                                                    class="list-group-item px-0 @if(!$loop->first) pt-3 @else pt-0 @endif @if(!$loop->last) pb-3 @else pb-0 @endif">
                                                                    <p class="mb-0 text-lg"><i class="fas fa-award me-2 text-warning"></i>{{ $pres->nama_prestasi }}
                                                                        <span class="text-muted">({{ ucfirst($pres->tingkat) }} -
                                                                            {{ $pres->tahun }})</span>
                                                                    </p>
                                                                    @if($pres->file_bukti && Storage::disk('public')->exists($pres->file_bukti))
                                                                        <a href="{{ Storage::url($pres->file_bukti) }}" target="_blank"
                                                                            class="btn-link text-sm">Lihat Bukti</a>
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                            @if($prestasiDisetujui->count() > 10)
                                                                <li class="list-group-item px-0 pt-3 pb-0 text-center">
                                                                    {{-- Pastikan route ini mengarah ke halaman daftar semua prestasi mahasiswa --}}
                                                                    <a href="{{ route('prestasi.mahasiswa.index') }}">Lihat semua
                                                                        prestasi...</a> 
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                @else
                                                    <div class="card-body">
                                                        <p class="mb-0 text-muted">Belum ada prestasi yang disetujui.</p>
                                                    </div>
                                                @endif
                                             @else
                                                <div class="card-body">
                                                    <p class="mb-0 text-muted">Belum ada prestasi yang ditambahkan.</p>
                                                </div>
                                            @endif
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
            padding-left: 10px;
        }

        @keyframes highlightAnimation {
            0% {
                background-color: rgba(13, 110, 253, 0.1);
            }

            70% {
                background-color: rgba(13, 110, 253, 0.1);
            }

            100% {
                background-color: transparent;
            }
        }

        .wid-auto {
            max-width: 100px;
            height: auto;
        }

        .text-lg {
            font-size: 1.05rem;
        }
    </style>
@endpush