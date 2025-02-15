<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARHAN PTPN I</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body {
        width: 100%;
        height: 1050px;
        overflow-x: hidden;
    }
</style>
</head>

<body class="relative overflow-y-auto min-h-screen flex flex-col justify-between" 
    style="background-image: url('assets/images/arhan-bg.png'); 
           background-size: 100% auto; /* Lebar penuh, tinggi otomatis */
           background-repeat: no-repeat;
           background-position: top center;
           ">

    <!-- Navbar -->
    <nav class="absolute w-full top-0 flex justify-between items-center px-8 py-4 bg-transparent">
        <div class="flex items-center gap-4">
            <!-- <img src="assets/images/logoptpn3.png" alt="PTPN 3" class="h-10">
            <img src="assets/images/logoptpn1.png" alt="PTPN 1" class="h-12"> -->
        </div>
        <div>
            <ul class="flex space-x-6 text-white text-lg">
                <li><a href="#" class="hover:text-gray-900"><b>Home</b></a></li>
                <li>
                    @auth
                    <a class="hover:text-gray-900" href="{{ route('admin.agenda.index') }}">
                        <b>Dashboard</b>
                    </a>
                    @else
                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#loginModal"
                        class="hover:text-gray-900">
                        <b>Masuk</b>
                    </a>
                    @endauth
                </li>
                <li><a href="/file-sekper/Manual-Book-Arhan.pdf" download class="hover:text-gray-900"><b>Manual</b></a></li>
                <li><a href="#" class="hover:text-gray-900"><b>Prosedur</b></a></li>
            </ul>
        </div>
    </nav>

    <!-- Content -->
    <!-- <div class="flex flex-col items-center justify-center h-screen text-white font-bold">
    <span class="text-8xl">ARHAN</span>
    <span class="text-5xl">Aplikasi Rapat Perusahaan PTPN I</span>
    </div>  -->

    <!-- Modal Login -->
    <div id="loginModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="loginModalLabel">Login ARHAN PTPN I</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" id="login_form">
                        @csrf
                        <div id="error-message" class="alert alert-danger d-none"></div>
                        <div class="mb-3">
                            <label class="form-label">Username:</label>
                            <input type="text" class="form-control" name="username">
                            <p id="error-message-username" class="text-danger d-none"></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password:</label>
                            <input type="password" class="form-control" name="password">
                            <p id="error-message-password" class="text-danger d-none"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="loginButton">
                                Login
                                <span id="loadingSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="absolute bottom-0 w-full text-center text-white py-3 bg-black bg-opacity-50">
        &copy; 2024 PT Perkebunan Nusantara I. All Rights Reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#login_form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Reset previous error messages
                $('#error-message').addClass('d-none').html('');
                $('#error-message-username').addClass('d-none').html('');
                $('#error-message-password').addClass('d-none').html('');

                // Disable the login button and show the loading spinner
                $('#loginButton').prop('disabled', true);
                $('#loadingSpinner').removeClass('d-none'); // Show the spinner

                $.ajax({
                    url: "{{ route('admin.login') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = "{{ route('admin.agenda.index') }}";
                        } else {
                            $('#error-message').html("Username/Password Salah").removeClass('d-none');
                        }
                    },
                    error: function(xhr) {
                        $('#error-message').html("Username/Password Salah").removeClass('d-none');
                    },
                    complete: function() {
                        // Re-enable the login button and hide the spinner after the request is complete
                        $('#loginButton').prop('disabled', false);
                        $('#loadingSpinner').addClass('d-none'); // Hide the spinner
                    }
                });
            });
        });
    </script>

</body>

</html>