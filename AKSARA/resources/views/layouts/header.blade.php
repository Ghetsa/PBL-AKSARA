<!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
<header class="pc-header">
  <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
    <div class="me-auto pc-mob-drp">
      <ul class="list-unstyled">
        <!-- ======= Menu collapse Icon ===== -->
        <li class="pc-h-item pc-sidebar-collapse">
          <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <li class="pc-h-item pc-sidebar-popup">
          <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <li class="dropdown pc-h-item d-inline-flex d-md-none">
          <a class="pc-head-link dropdown-toggle arrow-none m-0" data-bs-toggle="dropdown" href="#" role="button"
            aria-haspopup="false" aria-expanded="false">
            <i class="ti ti-search"></i>
          </a>
          <div class="dropdown-menu pc-h-dropdown drp-search">
            <form class="px-3">
              <div class="form-group mb-0 d-flex align-items-center">
                <i data-feather="search"></i>
                <input type="search" class="form-control border-0 shadow-none" placeholder="Search here. . .">
              </div>
            </form>
          </div>
        </li>
        {{-- <li class="pc-h-item d-none d-md-inline-flex">
          <form class="header-search">
            <i data-feather="search" class="icon-search"></i>
            <input type="search" class="form-control" placeholder="Search here. . .">
          </form>
        </li> --}}
      </ul>
    </div>
    <!-- [Mobile Media Block end] -->
    <div class="ms-auto">
      <ul class="list-unstyled">
        <li class="dropdown pc-h-item">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button"
            aria-haspopup="false" aria-expanded="false">
            <i class="ti ti-bell"></i>
            <span class="badge bg-success pc-h-badge">3</span>
          </a>
          <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
            <div class="dropdown-header d-flex align-items-center justify-content-between">
              <h5 class="m-0">Notification</h5>
              <a href="#!" class="pc-head-link bg-transparent"><i class="ti ti-circle-check text-success"></i></a>
            </div>
            <div class="dropdown-divider"></div>
            <div class="dropdown-header px-0 text-wrap header-notification-scroll position-relative"
              style="max-height: calc(100vh - 215px)">
              <div class="list-group list-group-flush w-100">
                <a class="list-group-item list-group-item-action">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="user-avtar bg-light-success"><i class="ti ti-gift"></i></div>
                    </div>
                    <div class="flex-grow-1 ms-1">
                      <span class="float-end text-muted">3:00 AM</span>
                      <p class="text-body mb-1">It's <b>Cristina danny's</b> birthday today.</p>
                      <span class="text-muted">2 min ago</span>
                    </div>
                  </div>
                </a>
                <a class="list-group-item list-group-item-action">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="user-avtar bg-light-primary"><i class="ti ti-message-circle"></i></div>
                    </div>
                    <div class="flex-grow-1 ms-1">
                      <span class="float-end text-muted">6:00 PM</span>
                      <p class="text-body mb-1"><b>Aida Burg</b> commented your post.</p>
                      <span class="text-muted">5 August</span>
                    </div>
                  </div>
                </a>
                <a class="list-group-item list-group-item-action">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="user-avtar bg-light-danger"><i class="ti ti-settings"></i></div>
                    </div>
                    <div class="flex-grow-1 ms-1">
                      <span class="float-end text-muted">2:45 PM</span>
                      <p class="text-body mb-1">Your Profile is Complete &nbsp;<b>60%</b></p>
                      <span class="text-muted">7 hours ago</span>
                    </div>
                  </div>
                </a>
                <a class="list-group-item list-group-item-action">
                  <div class="d-flex">
                    <div class="flex-shrink-0">
                      <div class="user-avtar bg-light-primary"><i class="ti ti-headset"></i></div>
                    </div>
                    <div class="flex-grow-1 ms-1">
                      <span class="float-end text-muted">9:10 PM</span>
                      <p class="text-body mb-1"><b>Cristina Danny </b> invited to join <b> Meeting.</b></p>
                      <span class="text-muted">Daily scrum meeting time</span>
                    </div>
                  </div>
                </a>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <div class="text-center py-2">
              <a href="{{ route('notifikasi.index') }}" class="link-primary">View all</a>
            </div>
          </div>
        </li>
        @php
      $user = Auth::user();
      $role = $user->role;
      $avatar = '';

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

        <li class="dropdown pc-h-item header-user-profile">
          <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button"
            aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
            <img src="{{ $avatar }}" alt="user-image" class="user-avtar rounded-circle img-fluid"
              style="width: 30px; height: 30px; object-fit: cover;"> {{-- GANTI DI SINI --}}
            <span>{{ Auth::user()->nama }}</span>
          </a>

          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown" style="min-width: 200px;">
            <div class="dropdown-header">
              <div class="d-flex mb-1 align-items-center">
                <div class="flex-shrink-0">
                  <img src="{{ $avatar }}" alt="user-image" class="user-avtar rounded-circle img-fluid"
                    style="width: 50px; height: 50px; object-fit: cover;">
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="mb-1">{{ Auth::user()->nama }}</h6>
                  <span>{{ ucfirst($role) ?? 'User' }}</span>
                </div>
              </div>
            </div>

            <a href="{{ route('profile.index') }}" class="dropdown-item">
              <i class="ti ti-user me-2"></i>
              <span>Lihat Profil</span>
            </a>
            {{-- <a href="#!" class="dropdown-item">
              <i class="ti ti-help"></i>
              <span>Dokumentasi</span>
            </a> --}}
            {{-- <a href="#!" class="dropdown-item">
              <i class="ti ti-key"></i>
              <span>Ubah Password</span>
            </a> --}}
            <a href="javascript:void(0);" onclick="openChangePasswordModal()" class="dropdown-item">
              <i class="ti ti-key"></i>
              <span>Ubah Password</span>
            </a>
            <a href="{{ route('logout') }}"
              onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">
              <i class="ti ti-power text-danger me-2"></i>
              <span class="text-danger">Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </div>
        </li>
      </ul>
    </div>
  </div>
</header>
<!-- [ Header ] end -->