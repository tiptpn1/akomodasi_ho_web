<div class="color-legend mb-2" id="legendInfo">
    @foreach ($jenis_rapat as $jr)
        <div>
            <div class="square" style="background-color: {{ $jr->kode_warna }}"></div>{{ $jr->nama }}
        </div>
    @endforeach
    <div>
        <div class="grey square"></div>Available
    </div>
</div>

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

            return $result[0];
        } else {
            $search = array_filter($allJam, function ($time) use ($timeVicon, $carbon) {
                $time_data = $carbon->parse(implode(':', explode('.', $time)));
                $time_vicon = $carbon->parse($timeVicon);

                return $time_data->diffInMinutes($time_vicon, false) >= 0;
            });

            $result = [...$search];

            return array_reverse($result)[0];
        }
    }
@endphp

<div class="table-overflow-x">
    <table class="table table-bordered" id="tableAgenda">
        <thead>
            <tr>
                <th scope="col" rowspan="2" class="align-middle">Jam</th>
                @foreach ($ruangan as $r)
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
                        <th scope="row" rowspan="{{ count($all_minutes) }}" class="align-middle">{{ $waktu }}</th>
                    @endif
                    @foreach ($ruangan as $r)
                        @php
                            $acara = '';
                            $rowspan = 1;
                            if ($r->sendVicons != null && count($r->sendVicons)) {
                                $timeCon = implode(':', explode('.', $waktu)) . ':00';
                                $vicon = $r->sendVicons
                                    ->filter(function ($vicon) use ($date, $timeCon, $carbon) {
                                        $startVicon = $carbon->parse($vicon->waktu);
                                        $endVicon = $carbon->parse($vicon->waktu2);
                                        $timeCon = $carbon->parse($timeCon);

                                        return $vicon->tanggal == $date &&
                                            $startVicon->diffInMinutes($timeCon, false) >= 0 &&
                                            $timeCon->diffInMinutes($endVicon, false) >= 0;
                                    })
                                    ->first();

                                $acara = $vicon != null ? $vicon->acara : '';
                                if ($vicon) {
                                    $startTime = calculateMinute($carbon, $all_jam_with_minutes, $vicon->waktu, 'start');
                                    $endTime = calculateMinute($carbon, $all_jam_with_minutes, $vicon->waktu2, 'end');
                                    $rowspan =
                                        array_search($endTime, $all_jam_with_minutes) -
                                        array_search($startTime, $all_jam_with_minutes) +
                                        1;
                                }
                            }
                        @endphp
                        @if ($acara)
                            @if ($rowspan == 1 || ($rowspan > 1 && $waktu == $startTime))
                                <td rowspan="{{ $rowspan }}" class="align-middle"
                                    style="background-color: {{ $vicon->jenisrapat->kode_warna }}; font-size: 14px; font-weight: bold; border: 0px solid black;">
                                    <div style="display: flex; flex-direction: column; justify-content: space-between;" onclick="detail('{{ $vicon->id }}')">
                                        <div>
                                            <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; ">{{ $acara }}</div><br />
                                            {{ explode(':', $vicon->waktu)[0] . '.' . explode(':', $vicon->waktu)[1]  }} - {{ explode(':', $vicon->waktu2)[0] . '.' . explode(':', $vicon->waktu2)[1] }}<br />
                                            {{ $vicon->bagian->master_bagian_nama }}<br />
                                            Peserta: {{ $vicon->jumlahpeserta != null? $vicon->jumlahpeserta . ' Orang' : '-' }}<br />
                                            PIC: {{ $vicon->personil }}<br />
                                        </div>
                                        <div class="text-right">
                                            <i class="fas fa-2x {{ $vicon->status_approval == 1? 'fa-check-circle' : 'fa-clock' }}" style="color: {{ $vicon->status_approval == 1? 'blue' : 'black' }}"></i>
                                        </div>
                                    </div>
                                </td>
                            @endif
                        @else
                            <td class="grey"></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
