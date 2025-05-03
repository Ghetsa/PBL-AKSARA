<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'Sample Page | Mantis Bootstrap 5 Admin Template')</title>
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
    @yield('page-css')
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
    <script src="{{ asset('mantis/dist/assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('mantis/dist/assets/js/plugins/feather.min.js') }}"></script>

    <script>layout_change('light');</script>
    <script>change_box_container('false');</script>
    <script>layout_rtl_change('false');</script>
    <script>preset_change("preset-1");</script>
    <script>font_change("Public-Sans");</script>
    @yield('page-js')
</body>
</html>