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

        .no-border {
            border: none;
        }
    </style>
</head>

<body>

    <table>
        <tr>
            <td style="text-align: center" colspan="10">Agenda</td>
        </tr>
        <tr>
            <td class="no-border" style="width: 100px;" colspan="2"><strong>Periode</strong></td>
            <td class="no-border" colspan="8">: {{ Carbon\Carbon::parse($startPeriode)->translatedFormat('d F Y') }}
                -
                {{ Carbon\Carbon::parse($endPeriode)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="no-border" colspan="2"><strong>Tanggal Jam Download</strong></td>
            <td class="no-border" colspan="8">: {{ $downloadTime }}</td>
        </tr>
    </table>


    <table>
        <thead>
            <tr>
                <th style="width: 30px; text-align:center; border: 1px solid black;">No</th>
                <th style="width: 125px; text-align:center; word-wrap: break-word; border: 1px solid black;">Hari,
                    Tanggal</th>
                <th style="width: 125px; text-align:center; word-wrap: break-word; border: 1px solid black;">Agenda</th>
                <th style="width: 125px; text-align:center; word-wrap: break-word; border: 1px solid black;">Waktu</th>
                <th style="width: 125px; text-align:center; word-wrap: break-word; border: 1px solid black;">Tempat</th>
                <th style="width: 125px; text-align:center; word-wrap: break-word; border: 1px solid black;">Dokumentasi
                    Humas</th>
                <th style="width: 125px; text-align:center; word-wrap: break-word; border: 1px solid black;">PIC/Bagian
                </th>
                <th style="width: 125px; text-align:center; word-wrap: break-word; border: 1px solid black;">Vicon</th>
                <th style="width: 125px; text-align:center; word-wrap: break-word; border: 1px solid black;">Peserta
                </th>
                <th style="width: 125px; text-align:center; word-wrap: break-word; border: 1px solid black;">Keterangan
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sendvicon as $vicon)
                <tr>
                    <td style="border: 1px solid black;">{{ $loop->iteration }}</td>
                    <td style="word-wrap: break-word; border: 1px solid black;">
                        {{ Carbon\Carbon::parse($vicon->tanggal)->translatedFormat('l, d F Y') }}
                    </td>
                    <td style="word-wrap: break-word; border: 1px solid black;">{{ $vicon->acara }}</td>
                    <td style="word-wrap: break-word; border: 1px solid black;">
                        {{ date('H:i', strtotime($vicon->waktu)) }} -
                        {{ date('H:i', strtotime($vicon->waktu2)) }}
                    </td>
                    <td style="word-wrap: break-word; border: 1px solid black;">
                        {{ $vicon->ruangan ?? $vicon->ruangan_lain }}
                    </td>
                    <td style="word-wrap: break-word; border: 1px solid black;">{{ $vicon->dokumentasi }}</td>
                    <td style="word-wrap: break-word; border: 1px solid black;">
                        {{ $vicon->bagian ? $vicon->bagian->bagian : '' }}
                    </td>
                    <td style="word-wrap: break-word; border: 1px solid black;">{{ $vicon->vicon }}</td>
                    <td style="word-wrap: break-word; border: 1px solid black;">{{ $vicon->peserta }}</td>
                    <td style="word-wrap: break-word; border: 1px solid black;">{{ $vicon->keterangan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
