<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Pemesanan Layanan Ruang Rapat Dan Video Conference</title>
    <script src="{{ asset('dist/counselor/js/jquery.min.js') }}"></script>

    <link href="{{ asset('dist/admin-lte/css/styles.css') }}" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">


    <!-- template index -->
    <link rel="stylesheet" href="{{ asset('dist/counselor/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/counselor/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/counselor/css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/counselor/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/counselor/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/counselor/css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
    <link rel="stylesheet" href="https://jonthornton.github.io/jquery-timepicker/jquery.timepicker.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.6/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.1.3/css/rowGroup.dataTables.min.css">

    <!-- template LTE -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('dist/admin-lte/css/adminlte.min.css') }}">

    <link rel="stylesheet" href="{{ asset('plugins/summernote/summernote-bs4.css') }}">

    <link rel="stylesheet" href="{{ asset('dist/admin-lte/css/adminlte.min.css') }}">


    <style type="text/css">
        /*.dropdown:hover .dropdown-menu {
         display: block !important;
      }*/
        .dropdown-menu {
            overflow-y: scroll;
            /* Add the ability to scroll */
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .dropdown-menu::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .dropdown-menu {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>

    <style type="text/css">
        .sb-sidenav-menu {
            overflow-y: scroll;
            /* Add the ability to scroll */
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .sb-sidenav-menu::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .sb-sidenav-menu {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }
    </style>

    {{ $styles ?? '' }}
    @stack('css')

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
</head>

<body class="sb-nav-fixed">
    <x-partials.navbar />



    <div id="layoutSidenav">
        <x-partials.sidebar />

        {{ $slot }}
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>
    <script src="{{ asset('dist/admin-lte/js/scripts.js') }}"></script>
    <script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('dist/admin-lte/js/adminlte.js') }}"></script>

    {{-- <script src="{{ asset('dist/counselor/js/jquery.min.js') }}"></script> --}}

    <script src="{{ asset('dist/counselor/js/jquery-migrate-3.0.1.min.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/popper.min.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/jquery.easing.1.3.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/jquery.waypoints.min.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/jquery.stellar.min.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/jquery.animateNumber.min.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/scrollax.min.js') }}"></script>
    <script src="{{ asset('dist/counselor/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script src="https://jonthornton.github.io/jquery-timepicker/jquery.timepicker.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.6/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.1.3/js/dataTables.rowGroup.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

    <link rel="stylesheet" href="https://jonthornton.github.io/jquery-timepicker/jquery.timepicker.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <script src="https://jonthornton.github.io/jquery-timepicker/jquery.timepicker.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

    {{ $scripts ?? '' }}

    @stack('js')
</body>

</html>
