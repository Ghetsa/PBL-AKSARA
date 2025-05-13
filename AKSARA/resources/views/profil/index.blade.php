@extends('layouts.template')

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
@endpush