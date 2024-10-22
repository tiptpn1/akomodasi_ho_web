<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Presensi Rapat</title>
    <style>
        /* Style global untuk semua tabel */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Styel tabel pertama */
        table.tabel-absensi {
            border: 1px solid black;
        }

        .table-header {
            text-align: center;
            font-weight: bold;
        }

        .table-body {
            text-align: left;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th colspan="5" style="text-align: center;">Data Presensi Rapat</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th colspan="2">Acara</th>
                <td colspan="3">: {{ $sendvicon->acara }}</td>
            </tr>
            <tr>
                <th colspan="2">Hari, Tanggal</th>
                <td colspan="3">:
                    {{ Carbon\Carbon::parse($sendvicon->tanggal)->translatedFormat('l') . ', ' . Carbon\Carbon::parse($sendvicon->tanggal)->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <th colspan="2">Waktu</th>
                <td colspan="3">:
                    {{ Carbon\Carbon::parse($sendvicon->waktu)->translatedFormat('H:i') . ' - ' . Carbon\Carbon::parse($sendvicon->waktu2)->translatedFormat('H:i') }}
                </td>
            </tr>
            <tr>
                <th colspan="2">Tempat</th>
                <td colspan="3">:
                    @if ($sendvicon->id_ruangan != null && $sendvicon->ruangan)
                        {{ $sendvicon->ruangan->nama }}
                    @else
                        {{ $sendvicon->ruangan_lain }}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    <table class="tabel-absensi">
        <thead>
            <tr>
                <th width="50px" style="text-align:center; font-weight:bold; border: 1px solid black;">No.</th>
                <th width="150px" style="text-align:center; font-weight:bold; border: 1px solid black;">Nama</th>
                <th width="150px" style="text-align:center; font-weight:bold; border: 1px solid black;">Jabatan</th>
                <th width="150px" style="text-align:center; font-weight:bold; border: 1px solid black;">Instansi</th>
                <th width="150px" style="text-align:center; font-weight:bold; border: 1px solid black;">Jam</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sendvicon->absensis as $absensi)
                <tr>
                    <td style="text-align:left; border: 1px solid black;">{{ $loop->iteration }}</td>
                    <td style="text-align:left; border: 1px solid black;">{{ $absensi->nama }}</td>
                    <td style="text-align:left; border: 1px solid black;">{{ $absensi->jabatan }}</td>
                    <td style="text-align:left; border: 1px solid black;">{{ $absensi->instansi }}</td>
                    <td style="text-align:left; border: 1px solid black;">
                        {{ Carbon\Carbon::parse($absensi->created)->format('H:i:s') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
