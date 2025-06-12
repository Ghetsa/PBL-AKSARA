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
                        <a class="nav-link btn btn-primary ms-lg-3" href="#">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary ms-lg-3" href="#">Tentang</a>
                    </li> --}}
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
            </div>
            <div class="col-md-6 illustration-container">
                <img src="{{ asset('mantis/dist/assets/images/slider/gedung.jpg') }}" alt="Ilustrasi Prestasi" class="img-fluid" style="max-height: 400px; width: 1000px;">
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
                        <i class="fas fa-bullhorn fa-3x mb-3" style="color: #FFD700;"></i>
                        <h5 class="card-title">Informasi Lomba</h5>
                        <p class="card-text">Temukan dan unggah berbagai informasi lomba yang relevan untuk mahasiswa.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($lombas) && $lombas->count() > 0)
        <section class="lomba-poster-section py-5">
            <div class="container">
                <h2 class="section-title text-center mb-4">Informasi Lomba Terbaru</h2>
                <p class="section-description text-center mb-5">Jangan lewatkan kesempatan untuk berprestasi. Temukan berbagai lomba menarik di sini!</p>
                <div class="row g-4">
                    @foreach($lombas as $lomba)
                    <div class="col-md-6 col-lg-4 d-flex align-items-stretch">
                        <div class="card shadow-sm h-100">
                            <a href="{{ $lomba->link_pendaftaran ?: ($lomba->link_penyelenggara ?: '#') }}" target="_blank" rel="noopener noreferrer" title="Kunjungi: {{ $lomba->nama_lomba }}" class="d-block text-decoration-none">
                                @if($lomba->poster && Storage::disk('public')->exists($lomba->poster))
                                    <img src="{{ asset('storage/' . $lomba->poster) }}" class="card-img-top" alt="Poster {{ $lomba->nama_lomba }}">
                                @else
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light text-muted" style="height: 260px;">
                                        <span>Poster Tidak Tersedia</span>
                                    </div>
                                @endif
                            </a>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ Str::limit($lomba->nama_lomba, 45) }}</h5>
                                <p class="card-text small text-muted">
                                    <i class="fas fa-sitemap fa-fw me-1"></i>Penyelenggara: {{ Str::limit($lomba->penyelenggara, 30) }}<br>
                                    <i class="fas fa-certificate fa-fw me-1"></i>Tingkat: {{ ucfirst($lomba->tingkat) }}<br>
                                    <i class="fas fa-tag fa-fw me-1"></i>Kategori: {{ ucfirst($lomba->kategori) }}<br>
                                    <i class="fas fa-calendar-times fa-fw me-1"></i>Batas Pendaftaran: {{ $lomba->batas_pendaftaran ? $lomba->batas_pendaftaran->format('d M Y') : 'N/A' }}
                                </p>
                                @if($lomba->link_pendaftaran)
                                    <a href="{{ $lomba->link_pendaftaran }}" class="btn btn-primary btn-sm w-100 mt-auto" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-external-link-alt me-1"></i> Daftar Lomba
                                    </a>
                                @elseif($lomba->link_penyelenggara)
                                    <a href="{{ $lomba->link_penyelenggara }}" class="btn btn-outline-secondary btn-sm w-100 mt-auto" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-info-circle me-1"></i> Info Selengkapnya
                                    </a>
                                @endif
                            </div>
                                @if($lomba->biaya > 0)
                                <div class="card-footer bg-transparent border-top-0 text-end pb-3">
                                    <span class="badge bg-light-primary text-primary p-2 badge-custom">Biaya: Rp {{ number_format($lomba->biaya, 0, ',', '.') }}</span>
                                </div>
                            @else
                                    <div class="card-footer bg-transparent border-top-0 text-end pb-3">
                                    <span class="badge bg-light-success text-success p-2 badge-custom">Gratis</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @if(Route::has('lomba.index'))
                <div class="text-center mt-5">
                    <a href="{{ route('lomba.index') }}" class="btn btn-lg btn-primary">
                        <i class="fas fa-search me-2"></i> Lihat Semua Info Lomba
                    </a>
                </div>
                @endif
            </div>
        </section>
        @endif
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

    <script src="{{ asset('mantis/dist/assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/jquery-validation/additional-methods.min.js') }}"></script>

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