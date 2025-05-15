<nav class="pc-sidebar">
  <div class="navbar-wrapper">
      <div class="m-header">
          <a href="{{ url('/') }}" class="b-brand text-primary">
              <img src="{{ asset('mantis/dist/assets/images/logo-dark.svg') }}" class="img-fluid logo-lg" alt="logo">
          </a>
      </div>
      @if (Auth::user()->role == 'admin')
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item">
                    <a href="{{ url('/dashboard/admin') }}" class="pc-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }} ">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Data Pengguna</label>
                    <i class="ti ti-dashboard"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/user') }}" class="pc-link  {{ ($activeMenu == 'user') ? 'active' : '' }} ">
                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                        <span class="pc-mtext">Data User</span>
                    </a>
                </li>
                <li class="pc-item pc-caption">
                    <label>Akademik</label>
                    <i class="ti ti-news"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/prodi') }}" class="pc-link {{ ($activeMenu == 'prodi') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-school"></i></span>
                        <span class="pc-mtext">Data Program Studi</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/periode') }}" class="pc-link {{ ($activeMenu == 'periode') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-calendar-time"></i></span>
                        <span class="pc-mtext">Data Periode Semester</span>
                    </a>
                </li>

                <li class="pc-item pc-caption">
                    <label>Data Lomba</label>
                    <i class="ti ti-brand-chrome"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/verifikasi') }}" class="pc-link {{ ($activeMenu == 'verifikasi') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-file-check"></i></span>
                        <span class="pc-mtext">Data Lomba</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/rekomendasi') }}" class="pc-link {{ ($activeMenu == 'rekomendasi') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-award"></i></span>
                        <span class="pc-mtext">Rekomendasi Lomba</span>
                    </a>
                </li>
                <li class="pc-item pc-caption">
                    <label>Data Prestasi</label>
                    <i class="ti ti-brand-chrome"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/admin/prestasi-verifikasi') }}" class="pc-link {{ ($activeMenu == 'prestasi') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-trophy"></i></span>
                        <span class="pc-mtext">Verifikasi Prestasi</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/laporan') }}" class="pc-link {{ ($activeMenu == 'laporan') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-report-analytics"></i></span>
                        <span class="pc-mtext">Laporan & Analisis</span>
                    </a>
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
                          <a href="#!" class="pc-link">Level 2.2<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                          <ul class="pc-submenu">
                              <li class="pc-item"><a class="pc-link" href="#!">Level 3.1</a></li>
                              <li class="pc-item"><a class="pc-link" href="#!">Level 3.2</a></li>
                              <li class="pc-item pc-hasmenu">
                                  <a href="#!" class="pc-link">Level 3.3<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                                  <ul class="pc-submenu">
                                      <li class="pc-item"><a class="pc-link" href="#!">Level 4.1</a></li>
                                      <li class="pc-item"><a class="pc-link" href="#!">Level 4.2</a></li>
                                  </ul>
                              </li>
                          </ul>
                      </li>
                      <li class="pc-item pc-hasmenu">
                          <a href="#!" class="pc-link">Level 2.3<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
                          <ul class="pc-submenu">
                              <li class="pc-item"><a class="pc-link" href="#!">Level 3.1</a></li>
                              <li class="pc-item"><a class="pc-link" href="#!">Level 3.2</a></li>
                              <li class="pc-item pc-hasmenu">
                                  <a href="#!" class="pc-link">Level 3.3<span class="pc-arrow"><i data-feather="chevron-right"></i></span></a>
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
                    <a href="{{ url('/dashboard/mahasiswa') }}" class="pc-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }} ">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="pc-item pc-caption">
                    <label>Data Lomba</label>
                    <i class="ti ti-brand-chrome"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/verifikasi') }}" class="pc-link {{ ($activeMenu == 'verifikasi') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-file-check"></i></span>
                        <span class="pc-mtext">Verifikasi</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/rekomendasi') }}" class="pc-link {{ ($activeMenu == 'rekomendasi') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-award"></i></span>
                        <span class="pc-mtext">Rekomendasi Lomba</span>
                    </a>
                </li>
                <li class="pc-item pc-caption">
                    <label>Prestasi</label>
                    <i class="ti ti-brand-chrome"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ url('mahasiswa/prestasi/create_ajax') }}" class="pc-link {{ ($activeMenu == 'prestasi') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-trophy"></i></span>
                        <span class="pc-mtext">Upload Prestasi</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ url('mahasiswa/prestasi') }}" class="pc-link {{ ($activeMenu == 'laporan') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-report-analytics"></i></span>
                        <span class="pc-mtext">Verifikasi Prestasi</span>
                    </a>
                </li>
            </ul>
        </div>
    @elseif (Auth::user()->role == 'dosen')
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item">
                    <a href="{{ url('/dashboard/dosen') }}" class="pc-link {{ ($activeMenu == 'dashboard') ? 'active' : '' }} ">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="pc-item pc-caption">
                    <label>Manajemen Mahasiswa</label>
                    <i class="ti ti-brand-chrome"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/bimbingan') }}" class="pc-link {{ ($activeMenu == 'bimbingan') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-file-check"></i></span>
                        <span class="pc-mtext">Mahasiswa Bimbingan</span>
                    </a>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/dosen/prestasi') }}" class="pc-link {{ ($activeMenu == 'prestasi') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-trophy"></i></span>
                        <span class="pc-mtext">Prestasi Mahasiswa</span>
                    </a>
                </li>
                <li class="pc-item pc-caption">
                    <label>Data Lomba</label>
                    <i class="ti ti-brand-chrome"></i>
                </li>
                <li class="pc-item">
                    <a href="{{ url('/lomba') }}" class="pc-link {{ ($activeMenu == 'lomba') ? 'active' : '' }}">
                        <span class="pc-micon"><i class="ti ti-award"></i></span>
                        <span class="pc-mtext">Daftar Lomba</span>
                    </a>
                </li>

            </ul>
        </div>
    @endif 
  </div>
</nav>