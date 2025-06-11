@push('styles')
<style>
    /* Style untuk notifikasi yang belum dibaca di dropdown */
    .list-group-item.unread-dropdown {
        background-color: #f0f3ff !important; /* Latar belakang biru sangat muda */
    }

    /* (Opsional) Style tambahan jika diperlukan */
    .dropdown-notification .list-group-item .fw-bold {
        color: #333; /* Membuat teks tebal lebih jelas */
    }
  .avatar-wrapper {
    width: 30px;
    height: 30px;
    flex-shrink: 0;
  }

  .avatar-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    display: block;
  }
</style>
@endpush

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
                @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                    <span class="badge bg-primary pc-h-badge">{{ $unreadNotificationCount }}</span>
                @endif
            </a>
            <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                <div class="dropdown-header d-flex align-items-center justify-content-between">
                    <h5 class="m-0">Notifikasi</h5>
                    @if(isset($unreadNotificationCount) && $unreadNotificationCount > 0)
                        {{-- Form untuk menandai semua sebagai terbaca --}}
                        <form action="{{ route(Auth::user()->role . '.notifikasi.markAllAsRead') }}" method="POST" id="mark-all-form" class="d-inline">
                            @csrf
                            <a href="#" onclick="event.preventDefault(); document.getElementById('mark-all-form').submit();" 
                              class="pc-head-link bg-transparent" title="Tandai semua dibaca">
                              <i class="ti ti-circle-check text-success"></i>
                            </a>
                        </form>
                    @endif
                </div>
                <div class="dropdown-divider"></div>

                <div class="dropdown-header px-0 text-wrap header-notification-scroll position-relative" style="max-height: calc(100vh - 215px)">
                    <div class="list-group list-group-flush w-100">
                        
                        @forelse ($recentNotifications as $notif)
                          @php
                              // Menentukan route detail berdasarkan role (sekarang sudah konsisten)
                              $detailRoute = '#';
                              try {
                                  $routeName = Auth::user()->role . '.notifikasi.show_and_read';
                                  if (Route::has($routeName)) {
                                      $detailRoute = route($routeName, ['id' => $notif->id, 'model' => $notif->type]);
                                  }
                              } catch (\Exception $e) {
                                  // Biarkan fallback ke '#' jika route tidak ditemukan
                              }
                          @endphp

                          <a href="javascript:void(0)" 
                            onclick="showNotificationDetail('{{ route(Auth::user()->role . '.notifikasi.show_and_read', ['id' => $notif->id, 'model' => $notif->type]) }}')"
                            class="list-group-item list-group-item-action {{ $notif->status_baca == 'belum_dibaca' ? 'unread-dropdown' : '' }}">
                                <div class="d-flex align-items-center">
                                  <div class="flex-shrink-0">
                                      
                                      <div class="user-avtar @if($notif->status_baca == 'dibaca') bg-light-success @else bg-light-primary @endif">
                                          <i class="ti @if($notif->status_baca == 'dibaca') ti-circle-check @else ti-info-circle @endif"></i>
                                      </div>

                                  </div>
                                  <div class="flex-grow-1 ms-3">
                                      <h6 class="mb-0 @if($notif->status_baca == 'belum_dibaca') fw-bold @endif">
                                          {{ $notif->judul }}
                                      </h6>
                                      <p class="text-muted mb-0" style="font-size: 0.85em;">{{ Str::limit($notif->isi, 40) }}</p>
                                      <small class="text-muted">{{ optional($notif->created_at)->diffForHumans() }}</small>
                                  </div>
                              </div>
                          </a>

                          {{-- <a href="{{ $detailRoute }}" 
                            class="list-group-item list-group-item-action @if($notif->status_baca == 'belum_dibaca') unread-dropdown @endif">
                              <div class="d-flex align-items-center">
                                  <div class="flex-shrink-0">
                                      
                                      <div class="user-avtar @if($notif->status_baca == 'dibaca') bg-light-success @else bg-light-primary @endif">
                                          <i class="ti @if($notif->status_baca == 'dibaca') ti-circle-check @else ti-info-circle @endif"></i>
                                      </div>

                                  </div>
                                  <div class="flex-grow-1 ms-3">
                                      <h6 class="mb-0 @if($notif->status_baca == 'belum_dibaca') fw-bold @endif">
                                          {{ $notif->judul }}
                                      </h6>
                                      <p class="text-muted mb-0" style="font-size: 0.85em;">{{ Str::limit($notif->isi, 40) }}</p>
                                      <small class="text-muted">{{ optional($notif->created_at)->diffForHumans() }}</small>
                                  </div>
                              </div>
                          </a> --}}
                      @empty
                          <div class="list-group-item">
                              <p class="text-center text-muted my-2">Tidak ada notifikasi baru.</p>
                          </div>
                      @endforelse
                    </div>
                  </div>
                <div class="dropdown-divider"></div>
              <div class="text-center py-2">
            @php
                $roleBasedRoute = route(Auth::user()->role . '.notifikasi.index');
            @endphp
            <a href="{{ $roleBasedRoute }}" class="link-primary">Lihat Semua Notifikasi</a>
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
              {{-- <img src="{{ $avatar }}" alt="user-image" class="user-avtar rounded-circle img-fluid" style="width: 30px; height: 30px; object-fit: cover; aspect-ratio: 1 / 1;"> --}}
              <img src="{{ $avatar }}" alt="user-image" class="user-avtar" style="width: 30px; height: 30px; object-fit: cover; aspect-ratio: 1 / 1;">
              <span>{{ Auth::user()->nama }}</span>
            </a>

          <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown" style="min-width: 200px;">
            <div class="dropdown-header">
              <div class="d-flex mb-1 align-items-center">
                <div class="flex-shrink-0">
                  <img src="{{ $avatar }}" alt="user-image" class="user-avtar rounded-circle img-fluid"
                    style="width: 50px; height: 50px; object-fit: cover;">
                </div>
                <div class="flex-grow-1 ms-3 text-wrap">
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