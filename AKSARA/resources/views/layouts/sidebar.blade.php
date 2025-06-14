<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div style="height: 60px; display: flex; align-items: center; padding: 16px 24px;">
            @if (Auth::user()->role == 'admin')
                <a href="{{ route('dashboard') }}" class="b-brand text-primary">
            @elseif (Auth::user()->role == 'dosen')
                    <a href="{{ url('/dashboard/dosen') }}" class="b-brand text-primary">
                @elseif (Auth::user()->role == 'mahasiswa')
                        <a href="{{ route('dashboard.mahasiswa') }}" class="b-brand text-primary">
                    @endif
                        <img src="{{ asset('logo/logo.svg') }}" class="img-fluid logo-lg" alt="logo">
                    </a>
        </div>
        {{-- @if(in_array(Auth::user()->role, ['mahasiswa', 'dosen']))
        <li class="pc-item {{ $activeMenu == 'info_lomba_user_group' ? 'active pc-trigger' : '' }}">
            <a href="#" class="pc-link">
                <span class="pc-micon"><i class="fas fa-trophy"></i></span>
                <span class="pc-mtext">Info Lomba</span>
                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            </a>
            <ul class="pc-submenu">
                <li class="pc-item {{ $activeMenu == 'info_lomba' ? 'active' : '' }}">
                    <a href="{{ route('lomba.index') }}" class="pc-link">Lihat Semua Lomba</a>
                </li>
                <li class="pc-item {{ $activeMenu == 'histori_lomba_user' ? 'active' : '' }}">
                    <a href="{{ route('lomba.histori.index') }}" class="pc-link">Histori Pengajuan Saya</a>
                </li>
            </ul>
        </li>
        @elseif(Auth::user()->role == 'admin')
        <li class="pc-item {{ $activeMenu == 'admin_lomba' ? 'active' : '' }}">
            <a href="{{ route('admin.lomba.index') }}" class="pc-link">
                <span class="pc-micon"><i class="fas fa-cogs"></i></span>
                <span class="pc-mtext">Manajemen Lomba</span>
            </a>
        </li>
        @endif --}}

        @if (Auth::user()->role == 'admin')
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <li class="pc-item">
                        <a href="{{ route('dashboard') }}"
                            class="pc-link {{ $activeMenu == 'dashboard' ? 'active' : '' }} ">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Beranda</span>
                        </a>
                    </li>

                    <li class="pc-item pc-caption">
                        <label>Data Pengguna</label>
                        <i class="ti ti-dashboard"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('/user') }}" class="pc-link  {{ $activeMenu == 'user' ? 'active' : '' }} ">
                            <span class="pc-micon"><i class="ti ti-users"></i></span>
                            <span class="pc-mtext">Data User</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Akademik</label>
                        <i class="ti ti-news"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('/prodi') }}" class="pc-link {{ $activeMenu == 'prodi' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-school"></i></span>
                            <span class="pc-mtext">Data Program Studi</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('/periode') }}" class="pc-link {{ $activeMenu == 'periode' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-calendar-time"></i></span>
                            <span class="pc-mtext">Data Periode Semester</span>
                        </a>
                    </li>

                    <li class="pc-item pc-caption">
                        <label>Lomba</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('admin.lomba.crud.index') }}"
                            class="pc-link {{ $activeMenu == 'lomba' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-award"></i></span>
                            <span class="pc-mtext">Data Lomba</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('admin.lomba.verifikasi.index') }}"
                            class="pc-link {{ $activeMenu == 'rekomendasi' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-file-certificate"></i></span>
                            <span class="pc-mtext">Verifikasi Lomba</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Prestasi Mahasiswa</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('/admin/prestasi-verifikasi') }}"
                            class="pc-link {{ $activeMenu == 'prestasi' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-trophy"></i></span>
                            <span class="pc-mtext">Verifikasi Prestasi</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('admin.laporan.index') }}" class="pc-link {{ $activeMenu == 'laporan' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-report-analytics"></i></span>
                            <span class="pc-mtext">Laporan & Analisis</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Keahlian Mahasiswa</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('/admin/keahlian-verifikasi') }}"
                            class="pc-link {{ $activeMenu == 'verifikasi_keahlian' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-certificate"></i></span>
                            <span class="pc-mtext">Verifikasi Keahlian</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Keluar</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="#" onclick="confirmLogout(event)" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-logout"></i></span>
                            <span class="pc-mtext">Keluar</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                        </form>
                    </li>
                    {{-- <li class="pc-item pc-hasmenu">
                        <a href="#!" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-menu"></i></span>
                            <span class="pc-mtext">Menu levels</span>
                            <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                        </a>
                        <ul class="pc-submenu">
                            <li class="pc-item"><a class="pc-link" href="#!">Level 2.1</a></li>
                            <li class="pc-item pc-hasmenu">
                                <a href="#!" class="pc-link">Level 2.2<span class="pc-arrow"><i
                                            data-feather="chevron-right"></i></span></a>
                                <ul class="pc-submenu">
                                    <li class="pc-item"><a class="pc-link" href="#!">Level 3.1</a></li>
                                    <li class="pc-item"><a class="pc-link" href="#!">Level 3.2</a></li>
                                    <li class="pc-item pc-hasmenu">
                                        <a href="#!" class="pc-link">Level 3.3<span class="pc-arrow"><i
                                                    data-feather="chevron-right"></i></span></a>
                                        <ul class="pc-submenu">
                                            <li class="pc-item"><a class="pc-link" href="#!">Level 4.1</a></li>
                                            <li class="pc-item"><a class="pc-link" href="#!">Level 4.2</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="pc-item pc-hasmenu">
                                <a href="#!" class="pc-link">Level 2.3<span class="pc-arrow"><i
                                            data-feather="chevron-right"></i></span></a>
                                <ul class="pc-submenu">
                                    <li class="pc-item"><a class="pc-link" href="#!">Level 3.1</a></li>
                                    <li class="pc-item"><a class="pc-link" href="#!">Level 3.2</a></li>
                                    <li class="pc-item pc-hasmenu">
                                        <a href="#!" class="pc-link">Level 3.3<span class="pc-arrow"><i
                                                    data-feather="chevron-right"></i></span></a>
                                        <ul class="pc-submenu">
                                            <li class="pc-item"><a class="pc-link" href="#!">Level 4.1</a></li>
                                            <li class="pc-item"><a class="pc-link" href="#!">Level 4.2</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li> --}}
                </ul>
            </div>
        @elseif (Auth::user()->role == 'mahasiswa')
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <li class="pc-item">
                        <a href="{{ route('dashboard.mahasiswa') }}"
                            class="pc-link {{ $activeMenu == 'dashboard' ? 'active' : '' }} ">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Beranda</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Info Lomba</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('lomba.mhs.histori.index') }}"
                            class="pc-link {{ $activeMenu == 'verifikasi' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-file-certificate"></i></span>
                            <span class="pc-mtext">Upload Info Lomba</span>
                        </a>
                    </li>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('/lomba') }}" class="pc-link {{ $activeMenu == 'rekomendasi' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-award"></i></span>
                            <span class="pc-mtext">Rekomendasi Lomba</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Prestasi</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('mahasiswa/prestasi') }}"
                            class="pc-link {{ $activeMenu == 'prestasi' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-trophy"></i></span>
                            <span class="pc-mtext">Prestasi Saya</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Keahlian</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('mahasiswa/keahlian_user') }}"
                            class="pc-link {{ $activeMenu == 'keahlian_user' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-certificate"></i></span>
                            <span class="pc-mtext">Keahlian & Sertifikasi</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Keluar</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="#" onclick="confirmLogout(event)" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-logout"></i></span>
                            <span class="pc-mtext">Keluar</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                        </form>
                    </li>
                </ul>
            </div>
        @elseif (Auth::user()->role == 'dosen')
            <div class="navbar-content">
                <ul class="pc-navbar">
                    <li class="pc-item">
                        <a href="{{ url('/dashboard/dosen') }}"
                            class="pc-link {{ $activeMenu == 'dashboard' ? 'active' : '' }} ">
                            <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                            <span class="pc-mtext">Beranda</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Manajemen Mahasiswa</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('/bimbingan') }}" class="pc-link {{ $activeMenu == 'bimbingan' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-report"></i></span>
                            <span class="pc-mtext">Mahasiswa Bimbingan</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ url('/dosen/prestasi') }}"
                            class="pc-link {{ $activeMenu == 'prestasi' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-trophy"></i></span>
                            <span class="pc-mtext">Prestasi Mahasiswa</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Info Lomba</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('lomba.dosen.index') }}"
                            class="pc-link {{ $activeMenu == 'lomba' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-award"></i></span>
                            <span class="pc-mtext">Daftar Lomba</span>
                        </a>
                    </li>
                    <li class="pc-item">
                        <a href="{{ route('lomba.dosen.histori.index') }}"
                            class="pc-link {{ $activeMenu == 'verifikasi' ? 'active' : '' }}">
                            <span class="pc-micon"><i class="ti ti-file-certificate"></i></span>
                            <span class="pc-mtext">Upload Info Lomba</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Keluar</label>
                        <i class="ti ti-brand-chrome"></i>
                    </li>
                    <li class="pc-item">
                        <a href="#" onclick="confirmLogout(event)" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-logout"></i></span>
                            <span class="pc-mtext">Keluar</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                        </form>
                    </li>
                </ul>
            </div>
        @endif


    </div>
</nav>