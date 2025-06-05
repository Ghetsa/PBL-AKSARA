<!DOCTYPE html>
<html lang="en">
<head>
  <title>Login | AKSARA</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="icon" href="{{ asset('mantis/dist/assets/images/favicon.svg') }}" type="image/x-icon">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap">
  <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/tabler-icons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/fontawesome.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/material.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/style-preset.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <style>
    .error {
      color: red;
      font-size: 0.875em;
      margin-top: 0.25rem;
    }
    .form-control.error {
      border-color: red;
    }
  </style>
</head>
<body>
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>

  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">
        <div class="auth-header">
          <a href="#"><img src="{{ asset('logo/logo.svg') }}" alt="logo"></a>
          {{-- <a href="#"><img src="{{ asset('mantis/dist/assets/images/logo-dark.svg') }}" alt="logo"></a> --}}
        </div>
        <div class="card my-5">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-end mb-4">
              <h3 class="mb-0"><b>Login</b></h3>
              <a href="{{ route('register') }}" class="link-primary">Belum punya akun?</a>
            </div>

            <form id="loginForm" method="POST" action="{{ url('postlogin') }}">
              @csrf
              <div class="form-group mb-3">
                <label class="form-label">NIP / NIM</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan NIP / NIM" required>
              </div>
              <div class="form-group mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
              </div>
              <div class="d-grid mt-4">
                <button type="submit" class="btn btn-primary" id="btnLogin">Login</button>
              </div>
            </form>

            <div class="saprator mt-3">
              <span>AKSARA</span>
            </div>
            {{-- <div class="row">
              <div class="col-4">
                <div class="d-grid">
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="{{ asset('mantis/dist/assets/images/authentication/google.svg') }}" alt="img"> <span class="d-none d-sm-inline-block">Google</span>
                  </button>
                </div>
              </div>
              <div class="col-4">
                <div class="d-grid">
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="{{ asset('mantis/dist/assets/images/authentication/twitter.svg') }}" alt="img"> <span class="d-none d-sm-inline-block">Twitter</span>
                  </button>
                </div>
              </div>
              <div class="col-4">
                <div class="d-grid">
                  <button type="button" class="btn mt-2 btn-light-primary bg-light text-muted">
                    <img src="{{ asset('mantis/dist/assets/images/authentication/facebook.svg') }}" alt="img"> <span class="d-none d-sm-inline-block">Facebook</span>
                  </button>
                </div>
              </div>
            </div> --}}
          </div>
        </div>

        <div class="auth-footer row">
          <div class="col my-1">
            <p class="m-0">Copyright © <a href="#">Codedthemes</a></p>
          </div>
          <div class="col-auto my-1">
            <ul class="list-inline footer-link mb-0">
              <li class="list-inline-item"><a href="{{ url('/') }}">Kembali</a></li>
            </ul>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- Required Js -->
  <script src="{{ asset('mantis/dist/assets/js/plugins/popper.min.js') }}"></script>
  <script src="{{ asset('mantis/dist/assets/js/plugins/simplebar.min.js') }}"></script>
  <script src="{{ asset('mantis/dist/assets/js/plugins/bootstrap.min.js') }}"></script>
  <script src="{{ asset('mantis/dist/assets/js/fonts/custom-font.js') }}"></script>
  <script src="{{ asset('mantis/dist/assets/js/pcoded.js') }}"></script>
  <script src="{{ asset('mantis/dist/assets/js/plugins/feather.min.js') }}"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <script>
    $(document).ready(function() {

      // Membuat Mixin untuk Toast Success
      const Toast = Swal.mixin({
        toast: true,
        position: 'top-end', // Posisi di pojok kanan atas
        showConfirmButton: false,
        timer: 1000, // Durasi 1 detik
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });

      $("#loginForm").validate({
        rules: {
          username: {
            required: true,
          },
          password: {
            required: true,
          }
        },
        messages: {
          username: {
            required: "NIM/NIP tidak boleh kosong",
          },
          password: {
            required: "Password tidak boleh kosong",
          }
        },
        errorPlacement: function(error, element) {
          error.addClass('invalid-feedback');
          element.closest('.mb-3').append(error);
        },
        highlight: function(element, errorClass, validClass) {
          $(element).addClass('is-invalid').removeClass('is-valid');
        },
        unhighlight: function(element, errorClass, validClass) {
          $(element).removeClass('is-invalid').addClass('is-valid');
        },
        submitHandler: function(form) {
          const formData = new FormData(form);
          
          fetch(form.action, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
              'Accept': 'application/json'
            },
            body: formData
          }).then(response => response.json())
            .then(res => {
              if (res.status) {
                // Gunakan Mixin Toast yang sudah dibuat
                Toast.fire({
                  icon: 'success',
                  title: res.message || 'Signed in successfully'
                }).then(() => {
                    window.location.href = res.redirect;
                });

              } else {
                // Menampilkan error umum dari server menggunakan SweetAlert biasa
                Swal.fire({
                  icon: 'error',
                  title: 'Login Gagal',
                  text: res.message || 'Terjadi kesalahan. Periksa kembali NIM/NIP dan password Anda.',
                });
              }
            }).catch(error => {
              console.error('Error:', error);
              Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
              });
            });
        }
      });
      
      // Menampilkan pesan dari session jika ada (misal setelah registrasi sukses)
      @if (session('success_swal'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: "{{ session('success_swal') }}",
        });
      @endif

    });
  </script>
  {{-- <script>
    document.getElementById('loginForm').addEventListener('submit', function (e) {
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
            alert(res.message || 'Login gagal');
          }
        }).catch(() => {
          alert('Terjadi kesalahan saat login.');
        });
    });
  </script> --}}

</body>
</html>
