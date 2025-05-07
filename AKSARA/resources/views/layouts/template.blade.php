<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'AKSARA')</title>
    
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Untuk mengirimkan token Laravel CSRF pada setiap request ajax -->
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
    <meta name="keywords" content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
    <meta name="author" content="CodedThemes">

    <link rel="icon" href="{{ asset('mantis/dist/assets/images/favicon.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="[https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap](https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap)" id="main-font-link">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/tabler-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/fonts/material.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/style-preset.css') }}">
      <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/plugins/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/plugins/responsive.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/plugins/buttons.bootstrap5.min.css') }}">

    <!-- SweetAlert2 CDN -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    {{-- <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/plugins/themes/borderless.css') }}">
    <link rel="stylesheet" href="{{ asset('mantis/dist/assets/css/plugins/themes/embed-iframe.css') }}"> --}}


    @yield('page-css')
    @stack('css')
</head>
<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    @include('layouts.sidebar')
    @include('layouts.header')
    <div class="pc-container">
        <div class="pc-content">
            @include('layouts.breadcrumb')
            @yield('content')
        </div>
    </div>
    @include('layouts.footer')
    <!-- jQuery -->
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}} 
    <script src="{{ asset('mantis/dist/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/bootstrap.min.js') }}"></script>

    <script src="{{ asset('mantis/dist/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/feather.min.js') }}"></script>

    <!-- DataTables & Plugins -->
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
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script> --}}
    {{-- Optional: SweetAlert2 untuk notifikasi yang lebih baik --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
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