<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Agenda pada Tanggal {{ $date }}</title>

    <style>
        .square {
            border: 1px solid rgb(126, 126, 126);
            width: 20px;
            height: 20px;
        }

        .grey {
            background-color: rgb(177, 177, 177);
        }

        th,
        td {
            font-size: 12px;
            border: 1px solid black;
        }

        table {
            width: 100% !important;
            border-collapse: collapse;
        }

        .table-overflow-x {
            width: 100% !important;
        }

        .mb-2 {
            margin-bottom: 10px;
        }

        .no-border {
            border: none !important;
        }

        .mb-4 {
            margin-bottom: 40px;
        }
    </style>
</head>

<body>
    @php
        $all_jam = [
            '07.00',
            '08.00',
            '09.00',
            '10.00',
            '11.00',
            '12.00',
            '13.00',
            '14.00',
            '15.00',
            '16.00',
            '17.00',
            '18.00',
            '19.00',
            '20.00',
            '21.00',
        ];

        $all_minutes = ['00', '30'];

        $all_jam_with_minutes = [];

        foreach ($all_jam as $key => $waktu) {
            if ($waktu != '21.00') {
                foreach ($all_minutes as $key => $minute) {
                    $all_jam_with_minutes[] = explode('.', $waktu)[0] . '.' . $minute;
                }
            } else {
                $all_jam_with_minutes[] = $waktu;
            }
        }

        function calculateMinute($carbon, $allJam, $timeVicon, $for)
        {
            if ($for == 'start') {
                $search = array_filter($allJam, function ($time) use ($timeVicon, $carbon) {
                    $time_data = $carbon->parse(implode(':', explode('.', $time)));
                    $time_vicon = $carbon->parse($timeVicon);

                    return $time_vicon->diffInMinutes($time_data, false) >= 0;
                });

                $result = [...$search];

                return count($result) > 0 ? $result[0] : $allJam[0];
            } else {
                $search = array_filter($allJam, function ($time) use ($timeVicon, $carbon) {
                    $time_data = $carbon->parse(implode(':', explode('.', $time)));
                    $time_vicon = $carbon->parse($timeVicon);

                    return $time_data->diffInMinutes($time_vicon, false) >= 0;
                });

                $result = [...$search];

                return array_reverse(count($result) > 0 ? $result : $allJam)[0];
            }
        }
    @endphp

    <h3 class="mb-2">PT Perkebunan Nusantara I <br /> <em>Head Office</em></h3>
    <div class="mb-2">
        <b>Tanggal: {{ $date }}</b>
    </div>
    <div class="mb-4">
        <small><em>Diunduh pada {{ date('d-m-Y H:i:s') }}</em></small>
    </div>

    @foreach ($all_lantai as $lantai)
        @php
            $end_time_vicon = [];
        @endphp
       
        <!-- <div class="mb-4" style="page-break-before: always; page-break-inside: avoid;"><div class="mb-2"> -->
        <div class="mb-4" style="page-break-inside: avoid;"><div class="mb-2">
            <b>Ruang Rapat 
                @if($lantai == 99)
                    Eksternal
                @else
                    pada Lantai {{ $lantai }}
                @endif
            </b>
        </div>
            <table class="mb-2" style="border-collapse: 1px !important;">
                @for ($i = 0; $i < intval(ceil((count($jenis_rapat) + 1) / 5)); $i++)
                    <tr>
                        @for ($j = 0; $j < 5; $j++)
                            @if ($i * 5 + $j < count($jenis_rapat))
                                <td class="no-border">
                                    <table>
                                        <tr>
                                            <td class="no-border" width="20px">
                                                <div class="square"
                                                    style="background-color: {{ $jenis_rapat[$i * 5 + $j]->kode_warna }}">
                                                </div>
                                            </td>
                                            <td class="no-border">{{ $jenis_rapat[$i * 5 + $j]->nama }}</td>
                                        </tr>
                                    </table>
                                </td>
                            @endif
                        @endfor
                        @if ($i == intval(ceil((count($jenis_rapat) + 1) / 5)) - 1)
                            <td class="no-border">
                                <table style="width: fit-content !important;">
                                    <tr>
                                        <td class="no-border" width="20px">
                                            <div class="grey square"></div>
                                        </td>
                                        <td class="no-border">Available</td>
                                    </tr>
                                </table>
                            </td>
                        @endif
                    </tr>
                @endfor
            </table>
        </div>

        <div class="table-overflow-x mb-4">
            @php
                $ruangan = $all_ruangan[$loop->iteration - 1];
            @endphp
            <table class="table table-bordered" id="tableAgenda">
                <thead>
                    <tr>
                        <th scope="col" rowspan="2" class="align-middle">Jam</th>
                        @foreach ($ruangan as $r)
                            @php
                                $end_time_vicon[$r->nama] = null;
                            @endphp
                            <th scope="col">{{ $r->nama }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($ruangan as $r)
                            <th scope="col">Kapasitas {{ $r->kapasitas }} orang</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($all_jam_with_minutes as $waktu)
                        <tr>
                            @if (in_array($waktu, $all_jam))
                                <th scope="row" rowspan="{{ $waktu != '21.00' ? count($all_minutes) : 1 }}"
                                    class="align-middle">{{ $waktu }}</th>
                            @endif
                            @foreach ($ruangan as $r)
                                @php
                                    $acara = '';
                                    $rowspan = 1;
                                    $vicon = '';
                                    if ($r->sendVicons != null && count($r->sendVicons)) {
                                        $timeCon = implode(':', explode('.', $waktu)) . ':00';
                                        $vicon = $r->sendVicons
                                            ->filter(function ($vicon) use ($timeCon, $end_time_vicon, $r, $carbon) {
                                                $startVicon = $carbon->parse($vicon->waktu);
                                                $endVicon = $carbon->parse($vicon->waktu2);
                                                $timeCon = $carbon->parse($timeCon);

                                                if ($end_time_vicon[$r->nama]) {
                                                    $endTimeVicon = $carbon->parse($end_time_vicon[$r->nama]);

                                                    return $startVicon->diffInMinutes($timeCon, false) >= 0 &&
                                                        $timeCon->diffInMinutes($endVicon, false) >= 0 &&
                                                        $endTimeVicon->diffInMinutes($startVicon, false) >= 0; // memastikan bahwa vicon tidak berada pada durasi $vicon lain
                                                } else {
                                                    return $startVicon->diffInMinutes($timeCon, false) >= 0 &&
                                                        $timeCon->diffInMinutes($endVicon, false) >= 0;
                                                }
                                            })
                                            ->first();

                                        $check_approve = null;

                                        /***
                                         * Cek, apabila $vicon memiliki status approval 0, apakah terdapat agenda lain yang sudah di approve
                                         * Jika ada maka $vicon akan diubah jadi null sehingga akan di skip untuk ditampilkan
                                         * **/
                                        if ($vicon) {
                                            if ($vicon->status_approval != 1) {
                                                $check_approve = $r->sendVicons
                                                    ->filter(function ($sendvicon) use ($vicon, $r, $carbon) {
                                                        $startSendVicon = $carbon->parse($sendvicon->waktu);
                                                        $startVicon = $carbon->parse($vicon->waktu);
                                                        $endVicon = $carbon->parse($vicon->waktu2);

                                                        return $sendvicon->status_approval == 1 &&
                                                            $startSendVicon->diffInMinutes($endVicon, false) >= 0 &&
                                                            $startVicon->diffInMinutes($startSendVicon, false) >= 0 &&
                                                            $sendvicon->id != $vicon->id &&
                                                            $r->id == $sendvicon->id_ruangan;
                                                    })
                                                    ->first();

                                                if ($check_approve) {
                                                    $vicon = null;
                                                }
                                            }
                                        }

                                        $acara = $vicon != null ? $vicon->acara : '';
                                        if ($vicon) {
                                            $startTime = calculateMinute(
                                                $carbon,
                                                $all_jam_with_minutes,
                                                $vicon->waktu,
                                                'start',
                                            );
                                            $endTime = calculateMinute(
                                                $carbon,
                                                $all_jam_with_minutes,
                                                $vicon->waktu2,
                                                'end',
                                            );
                                            $rowspan =
                                                array_search($endTime, $all_jam_with_minutes) -
                                                array_search($startTime, $all_jam_with_minutes) +
                                                1;

                                            // mencatat history waktu selesai $vicon
                                            $end_time_vicon[$r->nama] = $vicon->waktu2;
                                        }
                                    }
                                @endphp
                                @if ($acara)
                                    @if ($rowspan == 1 || ($rowspan > 1 && $waktu == $startTime))
                                        <td rowspan="{{ $rowspan }}" class="align-middle hover-pointer"
                                            style="background-color: {{ $vicon->jenisrapat->kode_warna }}; font-size: 14px; font-weight: bold; border: 0px solid black;"
                                            onclick="detail('{{ $vicon->id }}')">
                                            <div
                                                style="display: flex; flex-direction: column; justify-content: space-between;">
                                                <div>
                                                    <div
                                                        style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; ">
                                                        {{ $acara }}</div>
                                                    {{ explode(':', $vicon->waktu)[0] . '.' . explode(':', $vicon->waktu)[1] }}
                                                    -
                                                    {{ explode(':', $vicon->waktu2)[0] . '.' . explode(':', $vicon->waktu2)[1] }}<br />
                                                    {{ $vicon->bagian->master_bagian_nama }}<br />
                                                    Peserta:
                                                    {{ $vicon->jumlahpeserta != null ? $vicon->jumlahpeserta . ' Orang' : '-' }}<br />
                                                    PIC: {{ $vicon->personil }}<br />
                                                </div>
                                                <div class="text-right">
                                                    <i class="fas fa-2x {{ $vicon->status_approval == 1 ? 'fa-check-circle' : 'fa-clock' }}"
                                                        style="color: {{ $vicon->status_approval == 1 ? 'blue' : 'black' }}"></i>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                @else
                                    @if (
                                        $loop->parent->iteration - 1 >
                                            array_search(calculateMinute($carbon, $all_jam_with_minutes, $end_time_vicon[$r->nama], 'end'),
                                                $all_jam_with_minutes) || !$end_time_vicon[$r->nama]
                                    )
                                        <td class="grey"></td>
                                    @endif
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

</body>

</html>
