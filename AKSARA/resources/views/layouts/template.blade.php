<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.header')
</head>
<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('layouts.sidebar')

            <div class="layout-page">
                @include('layouts.breadcrumb')

                <div class="content-wrapper">
                    @yield('content')

                    @include('layouts.footer')
                    <div class="content-backdrop fade"></div>
                </div>
            </div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    @include('layouts.script')
</body>
</html>
