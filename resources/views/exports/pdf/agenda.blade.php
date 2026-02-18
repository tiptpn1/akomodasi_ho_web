<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rekap Agenda</title>

    <style>
        body {
            font-family: Helvetica;
            font-size: 14px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #888;
            text-align: center;
            padding: 5px;
        }

        table tr th {
            background-color: #888;
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1 style="text-align: center">Agenda</h1>
    <p>Periode : {{ Carbon\Carbon::parse($start_date)->translatedFormat('d F Y') }} -
        {{ Carbon\Carbon::parse($end_date)->translatedFormat('d F Y') }}</p>
    <p>Tanggal Jam Download : {{ $download_time }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Hari, Tanggal</th>
                <th>Agenda</th>
                <th>Waktu</th>
                <th>Tempat</th>
                <th>Dokumentasi Humas</th>
                <th>PIC</th>
                <th>Vicon</th>
                <th>Peserta</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sendvicon as $vicon)
                <tr>
                    <td style="width: 5%">{{ $loop->iteration }}</td>
                    <td style="width: 10%">{{ Carbon\Carbon::parse($vicon->tanggal)->translatedFormat('l') }},
                        {{ Carbon\Carbon::parse($vicon->tanggal)->translatedFormat('d F Y') }}</td>
                    <td style="width: 10%">{{ $vicon->acara }}</td>
                    <td style="width: 10%">{{ date('H:i', strtotime($vicon->waktu)) }} -
                        {{ date('H:i', strtotime($vicon->waktu2)) }}</td>
                    <td style="width: 10%">{{ $vicon->ruangan ?? $vicon->ruangan_lain }}</td>
                    <td style="width: 10%">{{ $vicon->dokumentasi }}</td>
                    <td style="width: 10%">{{ $vicon->bagian->bagian }}</td>
                    <td style="width: 10%">{{ $vicon->vicon }}</td>
                    <td style="width: 10%">{{ $vicon->peserta }}</td>
                    <td style="width: 15%">{{ $vicon->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
