<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Portal Akomodasi PTPN 1 Head Office</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
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

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

    <link rel="stylesheet" href="//jonthornton.github.io/jquery-timepicker/jquery.timepicker.css">

    <style type="text/css">
        .redboldfont {
            color: rgb(255, 0, 0);
            font-weight: bold;
        }

        .greenboldfont {
            color: rgb(0, 128, 0);
            font-weight: bold;
        }
    </style>
</head>

<body>
    <x-partials.guest-nav />

    {{ $slot }}

    <x-partials.guest-footer />

    <script src="{{ asset('dist/counselor/js/jquery.min.js') }}"></script>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js"></script>

    @stack('js')
</body>

</html>
