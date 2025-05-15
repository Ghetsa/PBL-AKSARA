<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'AKSARA')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}"> <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Sistem Informasi Pencatatan Prestasi">
    <meta name="keywords" content="Sistem Informasi, Pencatatan Prestasi, Mahasiswa, Dashboard">
    <meta name="author" content="Tim Pengembang">

    <link rel="icon" href="{{ asset('mantis/dist/assets/images/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/style-preset.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/plugins/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/plugins/buttons.bootstrap5.min.css') }}">

    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/plugins/sweetalert2/sweetalert2.min.css') }}">

    <style>
        /* Beberapa penyesuaian gaya tambahan agar lebih serasi dengan Mantis */
        .display-4 {
            font-weight: 700;
            color: #2c3e50; /* Warna heading gelap khas Mantis */
        }
        .lead {
            color: #6c757d; /* Warna teks muted Bootstrap */
        }
        .btn-primary:hover {
            border-radius: 10%;
            background-color: #2c3e50
        }
        .btn-outline-secondary {
            color: #6c757d;
            border-color: #6c757d;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
        }
        .card-text {
            color: #6c757d;
        }
        .bg-light-mantis {
            background-color: #f8f9fa; /* Warna background light khas Bootstrap */
        }
        .footer {
            background-color: #343a40; /* Warna footer gelap khas Bootstrap */
            color: #fff;
            padding: 2rem 0;
            text-align: center;
            margin-top: 3rem;
        }
        .footer a {
            color: #fff;
            text-decoration: none;
        }
        .footer a:hover {
            color: #eee;
        }
        .illustration-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        /* Styling untuk Navbar */
        .navbar {
            background-color: #fff; /* Warna background navbar Mantis (light) */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Efek shadow tipis navbar Mantis */
            padding: 1rem 0;
        }
        .navbar-brand {
            /* color: #2c3e50; */
            color: #343a40;
            font-weight: 800;
            font-size: 1.5rem;
        }
        .navbar-nav .nav-link {
            color: #555; /* Warna link navbar Mantis */
            margin-left: 1rem;
        }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            color: white
        }
        .navbar-toggler {
            border: none;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
    </style>
</head>
<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container">
            <a class="navbar-brand" href="#"><img src="{{ asset('logo/logo.svg') }}" alt="logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    {{-- <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Beranda</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary ms-lg-3" href="#">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary ms-lg-3" href="#">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary ms-lg-3" href="{{ route('login') }}">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary ms-lg-3" href="{{ route('register') }}">Daftar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-3">Aplikasi Kompetensi & Sarana Apresiasi</h1>
                <p class="lead text-muted mb-4">
                    AKSARA merupakan platform sistem informasi pencatatan prestasi yang terintegrasi untuk mencatat, mengelola, dan memantau prestasi mahasiswa secara efisien dan real-time.
                </p>
                {{-- Tombol Masuk dan Daftar dipindahkan ke Navbar --}}
            </div>
            <div class="col-md-6 illustration-container">
                <img src="{{ asset('mantis/dist/assets/images/slider/img-slide-1.jpg') }}" alt="Ilustrasi Prestasi" class="img-fluid" style="max-height: 400px;">
            </div>
        </div>

        <hr class="my-5">

        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body py-4">
                        <i class="fas fa-trophy fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Pencatatan Prestasi</h5>
                        <p class="card-text">Catat setiap pencapaian mahasiswa dengan mudah dan terstruktur.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body py-4">
                        <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                        <h5 class="card-title">Analisis Data</h5>
                        <p class="card-text">Pantau perkembangan prestasi melalui grafik dan laporan interaktif.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body py-4">
                        <i class="fas fa-users fa-3x text-warning mb-3"></i>
                        <h5 class="card-title">Manajemen Pengguna</h5>
                        <p class="card-text">Kelola peran dan akses pengguna sesuai kebutuhan institusi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} Aplikasi Kompetensi & Sarana Apresiasi. All rights reserved. | Design by <a href="#">Kelompok 4</a></p>
        </div>
    </footer>

    <script src="{{ asset('mantis/dist/assets/js/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/feather.min.js') }}"></script>

    <script src="{{ asset('mantis/dist/assets/js/plugins/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins//jszip.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins//pdfmake.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins//vfs_fonts.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/buttons.print.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/buttons.colVis.min.js') }}"></script>

    {{-- jQuery Validation Plugin --}}
    <script src="{{ asset('mantis/dist/assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/jquery-validation/additional-methods.min.js') }}"></script>

    {{-- Optional: SweetAlert2 untuk notifikasi yang lebih baik --}}
    <script src="{{ asset('mantis/dist/assets/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        // Untuk mengirimkan token Laravel CSRF pada setiap request ajax
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});

        // Kustomisasi default message jQuery Validation agar lebih sesuai Bootstrap 5
        $.extend($.validator.messages, {
            required: "Kolom ini wajib diisi.",
            remote: "Harap perbaiki kolom ini.",
            email: "Harap masukkan format email yang valid.",
            url: "Harap masukkan format URL yang valid.",
            date: "Harap masukkan format tanggal yang valid.",
            dateISO: "Harap masukkan format tanggal (ISO) yang valid.",
            number: "Harap masukkan angka yang valid.",
            digits: "Harap masukkan hanya digit.",
            creditcard: "Harap masukkan nomor kartu kredit yang valid.",
            equalTo: "Harap masukkan nilai yang sama lagi.",
            accept: "Harap masukkan nilai dengan ekstensi yang valid.",
            maxlength: $.validator.format("Harap masukkan tidak lebih dari {0} karakter."),
            minlength: $.validator.format("Harap masukkan setidaknya {0} karakter."),
            rangelength: $.validator.format("Harap masukkan nilai antara {0} dan {1} karakter."),
            range: $.validator.format("Harap masukkan nilai antara {0} dan {1}."),
            max: $.validator.format("Harap masukkan nilai kurang dari atau sama dengan {0}."),
            min: $.validator.format("Harap masukkan nilai lebih dari atau sama dengan {0}.")
        });

        // Kustomisasi penempatan error dan highlight untuk Bootstrap 5
        $.validator.setDefaults({
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.col-sm-10').append(error); // Sesuaikan selector jika struktur form berbeda
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            }
        });
    </script>
    @stack('js')

    <script>layout_change('light');</script>
    <script>change_box_container('false');</script>
    <script>layout_rtl_change('false');</script>
    <script>preset_change("preset-1");</script>
    <script>font_change("Public-Sans");</script>
    @yield('page-js')
</body>
</html>


 {{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Pencatatan Prestasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f3f4f6; /* Light background dari Mantis */
            color: #333;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #ffffff; /* White navbar dari Mantis */
            box-shadow: 0 1px 3px rgba(0,0,0,0.1); /* Box shadow tipis dari Mantis */
            padding: 1rem 0;
        }
        .navbar-brand {
            color: #4c51bf; /* Warna primer Mantis (mungkin perlu disesuaikan) */
            font-weight: 600;
            font-size: 1.8rem;
        }
        .navbar-nav .nav-link {
            color: #555; /* Warna teks nav link dari Mantis */
            margin-left: 1rem;
            font-weight: 400;
        }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            color: #4c51bf; /* Warna hover/active dari Mantis */
        }
        .btn-primary {
            background-color: #4c51bf; /* Warna tombol primer Mantis */
            border-color: #4c51bf;
            color: white;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #3b419e; /* Warna hover tombol primer Mantis */
            border-color: #3b419e;
        }
        .hero-section {
            background-color: #e9ecef; /* Light gray dari Mantis */
            padding: 4rem 0;
            text-align: center;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .hero-title {
            font-size: 2.5rem;
            color: #2c3e50; /* Warna judul dark gray */
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .hero-description {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            max-width: 70%;
            margin-left: auto;
            margin-right: auto;
        }
        .feature-section {
            padding: 4rem 0;
            text-align: center;
        }
        .feature-title {
            font-size: 2rem;
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .feature-description {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            max-width: 70%;
            margin-left: auto;
            margin-right: auto;
        }
        .feature-card {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08); /* Shadow lebih tipis */
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            margin-bottom: 2rem;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Efek hover lebih halus */
        }
        .feature-icon {
            font-size: 3rem;
            color: #4c51bf; /* Warna ikon Mantis */
            margin-bottom: 1rem;
        }
        .feature-card-title {
            font-size: 1.5rem;
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .feature-card-text {
            font-size: 1rem;
            color: #666;
        }
        .footer {
            background-color: #e9ecef; /* Warna footer dari Mantis */
            padding: 2rem 0;
            text-align: center;
            color: #555;
            margin-top: 4rem;
        }
        .footer a {
            color: #4c51bf; /* Warna link footer */
            text-decoration: none;
        }
        .footer a:hover {
            color: #3b419e; /* Warna link hover footer */
            text-decoration: underline;
        }

        /* Responsive Styles (Optional - sudah dihandle Bootstrap, tapi bisa disesuaikan) */
        @media (max-width: 992px) {
            .navbar-nav .nav-link {
                margin-left: 0;
                margin-right: 0;
            }
            .hero-title {
                font-size: 2rem;
            }
            .hero-description {
                font-size: 1rem;
            }
            .feature-title {
                font-size: 1.75rem;
            }
            .feature-description {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem Informasi Prestasi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary" href="#">Masuk / Daftar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="container">
            <h1 class="hero-title">Kelola dan Pantau Prestasi dengan Mudah</h1>
            <p class="hero-description">Platform terpusat untuk mencatat, memvalidasi, dan menampilkan prestasi siswa.</p>
            <a href="#" class="btn btn-primary">Mulai Sekarang</a>
        </div>
    </header>

    <main class="feature-section">
        <div class="container">
            <h2 class="feature-title">Fitur Utama</h2>
            <p class="feature-description">Sistem ini menyediakan berbagai fitur untuk memudahkan pengelolaan dan pemantauan prestasi.</p>
            <div class="row">
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <i class="fas fa-trophy feature-icon"></i>
                        <h3 class="feature-card-title">Pencatatan Prestasi</h3>
                        <p class="feature-card-text">Catat berbagai jenis prestasi siswa secara detail.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <i class="fas fa-users-check feature-icon"></i>
                        <h3 class="feature-card-title">Verifikasi Data</h3>
                        <p class="feature-card-text">Proses verifikasi yang transparan untuk memastikan keabsahan data.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <i class="fas fa-chart-bar feature-icon"></i>
                        <h3 class="feature-card-title">Laporan dan Statistik</h3>
                        <p class="feature-card-text">Hasilkan laporan komprehensif dan statistik prestasi.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <i class="fas fa-calendar-alt feature-icon"></i>
                        <h3 class="feature-card-title">Manajemen Kegiatan</h3>
                        <p class="feature-card-text">Kelola kegiatan-kegiatan yang terkait dengan prestasi.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <i class="fas fa-user-shield feature-icon"></i>
                        <h3 class="feature-card-title">Akses Terkontrol</h3>
                        <p class="feature-card-text">Sistem hak akses untuk berbagai peran pengguna.</p>
                    </div>
                </div>
                 <div class="col-md-6 col-lg-4">
                    <div class="feature-card">
                        <i class="fas fa-bullhorn feature-icon"></i>
                        <h3 class="feature-card-title">Pengumuman</h3>
                        <p class="feature-card-text">Fitur pengumuman prestasi terbaru.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Sistem Informasi Pencatatan Prestasi. All rights reserved. | Design by <a href="https://example.com">Tim Pengembang</a></p>
        </div>
    </footer>

    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> --}}
