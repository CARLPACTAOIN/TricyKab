<!DOCTYPE html>
<html lang="en" dir="ltr" data-nav-layout="vertical" class="light" data-header-styles="light" data-menu-styles="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> @yield('title', 'Valex - Tailwind Admin Template') </title>
    <meta name="description" content="Valex Admin Template">
    <meta name="keywords" content="admin, dashboard">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/brand-logos/favicon.ico') }}">

    <!-- Styles -->
    @include('layouts.partials.styles')
</head>

<body>

    @include('layouts.partials.switcher')
    @include('layouts.partials.loader')

    <div class="page">
        @include('layouts.partials.header')
        @include('layouts.partials.sidebar')

        <div class="content">
            <!-- Start::app-content -->
            <div class="main-content app-content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
            <!-- End::app-content -->

            @include('layouts.partials.footer')
        </div>
    </div>

    <!-- Back To Top -->
    <div class="scrollToTop">
        <span class="arrow"><i class="ri-arrow-up-s-fill text-xl"></i></span>
    </div>

    <div id="responsive-overlay"></div>

    @include('layouts.partials.modals')
    @include('layouts.partials.scripts')

</body>

</html>
