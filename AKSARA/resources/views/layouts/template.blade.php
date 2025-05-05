<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'Sample Page | Mantis Bootstrap 5 Admin Template')</title>
    
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

    <!-- jQuery Validation Plugin -->
    <script src="{{ asset('mantis/dist/assets/js/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/jquery-validation/additional-methods.min.js') }}"></script>

    <!-- SweetAlert2 -->
    <script src="{{ asset('mantis/dist/assets/js/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        // Untuk mengirimkan token Laravel CSRF pada setiap request ajax
        $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
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