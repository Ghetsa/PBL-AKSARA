<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>Register | AKSARA</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description"
    content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
  <meta name="keywords"
    content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
  <meta name="author" content="CodedThemes">

  <!-- [Favicon] icon -->
  <link rel="icon" href="mantis/dist/assets/images/favicon.svg" type="image/x-icon"> <!-- [Google Font] Family -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
    id="main-font-link">
  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="mantis/dist/assets/fonts/tabler-icons.min.css">
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="mantis/dist/assets/fonts/feather.css">
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="mantis/dist/assets/fonts/fontawesome.css">
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="mantis/dist/assets/fonts/material.css">
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="mantis/dist/assets/css/style.css" id="main-style-link">
  <link rel="stylesheet" href="mantis/dist/assets/css/style-preset.css">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->

  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="#"><img src="{{ asset('logo/logo.svg') }}" alt="img"></a>
        </div>
        <div class="card my-5">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-end mb-4">
              <h3 class="mb-0"><b>Register</b></h3>
              <a href="{{ route('login') }}" class="link-primary">Sudah punya akun?</a>
            </div>
            <form id="registerForm" method="POST" action="{{ route('register') }}">
              @csrf
              <div class="form-group mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama">
              </div>
              <div class="form-group mb-3">
                <label class="form-label">NIM</label>
                <input type="text" name="nim" class="form-control" placeholder="NIM">
              </div>
              <div class="form-group mb-3">
                <label class="form-label">Alamat Email*</label>
                <input type="email" name="email" class="form-control" placeholder="Alamat Email">
              </div>
              <div class="form-group mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password">
              </div>
              <div class="form-group mb-3">
                <label for="prodi_id" class="form-label">Program Studi</label>
                {{-- <div class="form-control"> --}}
                  <select class="form-select" id="prodi_id" name="prodi_id">
                    <option value="">- Pilih Program Studi -</option>
                    @if(isset($prodi))
                  @foreach($prodi as $item)
              <option value="{{ $item->prodi_id }}">{{ $item->nama }}</option>
              @endforeach
          @endif
                  </select>
                  <span class="invalid-feedback error-text" id="error-prodi_id"></span>
                  {{--
                </div> --}}
              </div>
              <div class="form-group mb-3">
                <label for="periode_id" class="form-label">Semester</label>
                {{-- <div class="form-control"> --}}
                  <select class="form-select" id="periode_id" name="periode_id">
                    <option value="">- Pilih Semester -</option>
                    @if(isset($periode))
                  @foreach($periode as $item)
              <option value="{{ $item->periode_id }}">{{ $item->tahun_akademik }} / {{ $item->semester }}</option>
              @endforeach
          @endif
                  </select>
                  <span class="invalid-feedback error-text" id="error-prodi_id"></span>
                  {{--
                </div> --}}
              </div>
              {{-- <p class="mt-4 text-sm text-muted">By Signing up, you agree to our <a href="#" class="text-primary">
                  Terms of Service </a> and <a href="#" class="text-primary"> Privacy Policy</a></p> --}}
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary">Buat Akun</button>
              </div>
            </form>
            <div class="saprator mt-3">
              <span>AKSARA</span>
            </div>

          </div>
        </div>
        <div class="auth-footer row">
          <!-- <div class=""> -->
          <div class="col my-1">
            <p class="m-0">Copyright © <a href="#">Codedthemes</a></p>
          </div>
          <div class="col-auto my-1">
            <ul class="list-inline footer-link mb-0">
              <li class="list-inline-item"><a href="#">Home</a></li>
              <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
              <li class="list-inline-item"><a href="#">Contact us</a></li>
            </ul>
          </div>
          <!-- </div> -->
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->
  <!-- Required Js -->
  <script src="mantis/dist/assets/js/plugins/popper.min.js"></script>
  <script src="mantis/dist/assets/js/plugins/simplebar.min.js"></script>
  <script src="mantis/dist/assets/js/plugins/bootstrap.min.js"></script>
  <script src="mantis/dist/assets/js/fonts/custom-font.js"></script>
  <script src="mantis/dist/assets/js/pcoded.js"></script>
  <script src="mantis/dist/assets/js/plugins/feather.min.js"></script>

  <!-- AJAX Register Script -->
  <script>
    document.getElementById('registerForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const form = e.target;
      const data = new FormData(form);

      fetch(form.action, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        },
        body: data
      }).then(res => res.json())
        .then(res => {
          if (res.status) {
            window.location.href = res.redirect;
          } else {
            alert(res.message || 'Registrasi gagal');
          }
        }).catch(() => {
          alert('Terjadi kesalahan saat registrasi.');
        });
    });
  </script>

</html>