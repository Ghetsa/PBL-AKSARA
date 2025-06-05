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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <style>
    .error {
      color: red;
      font-size: 0.875em;
      margin-top: 0.25rem;
    }
    .form-control.error, .form-select.error {
      border-color: red;
    }
  </style>

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
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email">
              </div>
              <div class="form-group mb-3">
                <label class="form-label">Nomor Telepon</label>
                <input type="no_telepon" name="no_telepon" class="form-control" placeholder="Nomor Telepon">
              </div>
              <div class="form-group mb-3">
                <label class="form-label">Alamat</label>
                <input type="alamat" name="alamat" class="form-control" placeholder="Alamat">
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
                <label for="periode_id" class="form-label">Periode</label>
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
              <li class="list-inline-item"><a href="{{ url('/') }}">Kembali</a></li>
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/additional-methods.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        $.validator.addMethod("nimCheck", function(value, element) {
            return this.optional(element) || /^[0-9]{10}$/.test(value);
        }, "NIM harus terdiri dari 10 digit angka.");

        $.validator.addMethod("phoneID", function(phone_number, element) {
            phone_number = phone_number.replace(/\s+/g, "");
            return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^(08[0-9]{8,11}|(\+62|62)8[0-9]{8,11})$/);
        }, "Silakan masukkan nomor telepon yang valid.");

      $("#registerForm").validate({
        rules: {
          nama: { required: true, minlength: 3 },
          email: { required: true, email: true },
          nim: { required: true, nimCheck: true },
          prodi_id: { required: true },
          periode_id: { required: true },
          no_telepon: { required: true, phoneID: true },
          alamat: { required: true, minlength: 10 },
          password: { required: true, minlength: 8 },
          password_confirmation: { required: true, minlength: 8, equalTo: "#password" }
        },
        messages: {
          nama: { required: "Nama lengkap tidak boleh kosong", minlength: "Nama minimal 3 karakter" },
          email: { required: "Email tidak boleh kosong", email: "Format email tidak valid" },
          nim: { required: "NIM tidak boleh kosong" },
          prodi_id: { required: "Program studi harus dipilih" },
          periode_id: { required: "Periode masuk harus dipilih" },
          no_telepon: { required: "Nomor telepon tidak boleh kosong" },
          alamat: { required: "Alamat tidak boleh kosong", minlength: "Alamat minimal 10 karakter" },
          password: { required: "Password tidak boleh kosong", minlength: "Password minimal 8 karakter" },
          password_confirmation: { required: "Konfirmasi password tidak boleh kosong", minlength: "Konfirmasi password minimal 8 karakter", equalTo: "Konfirmasi password tidak cocok" }
        },
        errorElement: 'label',
        errorClass: 'error',
        errorPlacement: function(error, element) {
          if (element.is('select')) {
            error.insertAfter(element.closest('.mb-3').find('.form-select'));
          } else {
            error.insertAfter(element);
          }
        },
        highlight: function(element, errorClass, validClass) {
          $(element).addClass('error').removeClass(validClass);
          $(element).closest('.mb-3').addClass('has-error');
        },
        unhighlight: function(element, errorClass, validClass) {
          $(element).removeClass('error').addClass(validClass);
          $(element).closest('.mb-3').removeClass('has-error');
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
                    Swal.fire({
                        icon: 'success',
                        title: 'Registrasi Berhasil!',
                        text: res.message,
                        timer: 2000, // Menutup otomatis setelah 2 detik
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = res.redirect;
                    });
                } else {
                    // Jika ada error spesifik per field dari backend
                    if (res.msgField) {
                        let errorMessages = [];
                        $.each(res.msgField, function(key, value) {
                            errorMessages.push(value[0]); // Ambil pesan error pertama untuk setiap field
                            let inputField = $(form).find('[name="' + key + '"]');
                            let errorContainer = inputField.closest('.mb-3');
                            inputField.addClass('is-invalid');
                            let errorLabel = errorContainer.find('#' + key + '-error-server');
                            if(errorLabel.length === 0){
                                errorContainer.append('<label id="' + key + '-error-server" class="error invalid-feedback d-block" for="' + key + '"></label>'); // d-block agar tampil
                                errorLabel = errorContainer.find('#' + key + '-error-server');
                            }
                            errorLabel.html(value[0]).show();
                        });
                        // Tampilkan ringkasan error menggunakan SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Registrasi Gagal',
                            html: 'Terdapat kesalahan pada input Anda:<br>' + errorMessages.join('<br>'),
                        });
                    } else if (res.message) {
                        // Tampilkan error umum jika tidak ada error spesifik field
                        Swal.fire({
                            icon: 'error',
                            title: 'Registrasi Gagal',
                            text: res.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registrasi Gagal',
                            text: 'Terjadi kesalahan yang tidak diketahui.',
                        });
                    }
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
    });
  </script>
  {{-- <script>
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
  </script> --}}

</html>