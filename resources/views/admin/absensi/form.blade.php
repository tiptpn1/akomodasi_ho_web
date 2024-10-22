<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PTPN XII | Presensi Rapat</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('dist/admin-lte/css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>

<body class="hold-transition login-page">
    <div class="col-md-6 mt-3">
        <div class="login-logo">
            <a href="{{ route('home') }}"><b>Presensi Rapat </b>PTPN XII</a>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">
                    <b>{{ $sendvicon->acara }}</b>
                </p>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <table class="display responsive mb-4" style="width: 100%; float:center;">
                    <tbody>
                        <tr>
                            <th>Hari, Tanggal</th>
                            <td><b>:</b></td>
                            <td>
                                <b>
                                    {{ Carbon\Carbon::parse($sendvicon->tanggal)->translatedFormat('l') . ', ' . Carbon\Carbon::parse($sendvicon->tanggal)->translatedFormat('d F Y') }}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td><b>:</b></td>
                            <td>
                                <b>
                                    {{ Carbon\Carbon::parse($sendvicon->waktu)->translatedFormat('H:i') . ' - ' . Carbon\Carbon::parse($sendvicon->waktu2)->translatedFormat('H:i') }}
                                </b>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <form action="{{ route('absensi.store') }}" method="POST">
                    @csrf
                    <input type="text" name="id" value="{{ $sendvicon->id }}" hidden>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap *</label>
                        <input type="text" id="nama" name="nama" class="form-control"
                            placeholder="Isikan Nama Lengkap" required>
                    </div>
                    <div class="form-group">
                        <label for="jabatan">Jabatan *</label>
                        <input type="text" id="jabatan" name="jabatan" class="form-control"
                            placeholder="Isikan Jabatan" required>
                    </div>
                    <div class="form-group">
                        <label for="instansi">Instansi *</label>
                        <input type="text" id="instansi" name="instansi" class="form-control"
                            placeholder="Isikan Instansi" required>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="login-logo">
            <p>Daftar Presensi</p>
        </div>

        @if ($sendvicon->absensis->count() > 0)
            <div class="card">
                <div class="table-responsive">
                    <table class="display responsive table-light table table-striped" style="width: 100%; float:center;"
                        id="dataTables-example">
                        <thead>
                            <tr>
                                <th>
                                    <center>No</center>
                                </th>
                                <th>
                                    <center>Nama</center>
                                </th>
                                <th>
                                    <center>Jabatan</center>
                                </th>
                                <th>
                                    <center>Instansi</center>
                                </th>
                                <th>
                                    <center>Jam</center>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sendvicon->absensis as $absensi)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $absensi->nama }}</td>
                                    <td>{{ $absensi->jabatan }}</td>
                                    <td>{{ $absensi->instansi }}</td>
                                    <td style="text-align: center;">
                                        {{ Carbon\Carbon::parse($absensi->created)->format('H:i:s') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <p style="text-align: center;">Belum terdapat data presensi</p>
        @endif
    </div>


    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/admin-lte/js/adminlte.min.js') }}"></script>
</body>

</html>
