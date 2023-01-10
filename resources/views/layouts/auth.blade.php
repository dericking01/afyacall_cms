<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon.png') }}">
    <title>AfyaCall</title>

    @stack('before-styles')

    <!-- Bootstrap Core CSS -->

    <link href="{{ asset('assets/plugins/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Template CSS -->

    @section('template-css')
        <link href="{{ asset('css/monster/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/colors/blue.css') }}" id="theme" rel="stylesheet">
    @show

    @stack('after-styles')

</head>

<body class=" @yield('body-classes') card-no-border ">
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> </svg>
    </div>

    <div id="main-wrapper">
        @yield('layout-content')
    </div>

    @stack('before-scripts')

    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    @section('jquery')
        <script src="{{ asset('assets/plugins/jquery/jquery.min.js') }}"></script>
    @show
    <!-- Bootstrap tether Core JavaScript -->
    <script src="{{ asset('assets/plugins/popper/popper.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="{{ asset('vendor/js/jquery.slimscroll.js') }}"></script>
    <!--Wave Effects -->
    <script src="{{ asset('vendor/js/waves.js') }}"></script>
    <!--Menu sidebar -->
    <script src="{{ asset('vendor/js/sidebarmenu.js') }}"></script>
    <!--stickey kit -->
    <script src="{{ asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') }}"></script>

    <!--Custom JavaScript -->
    @section('template-custom-js')
        <script src="{{ asset('vendor/js/custom.min.js') }}"></script>
    @show
    <!-- ============================================================== -->
    <!-- Style switcher -->
    <!-- ============================================================== -->
    <script src="{{ asset('assets/plugins/styleswitcher/jQuery.style.switcher.js') }}"></script>


    @stack('after-scripts')

</body>

</html>
