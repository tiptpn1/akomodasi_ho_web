<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Jadwal Driver</title>
    <style>
        /* Mengatur halaman agar tidak memiliki margin */
        @page {
            margin: 10px;
        }

        body {
            font-family: sans-serif;
            font-size: 10px;
            /* --- PERBAIKAN: Hapus properti transform agar pas otomatis --- */
            /* Properti transform dan transform-origin dihapus */
        }

        .table {
            width: 100%; /* Memastikan tabel menggunakan lebar penuh */
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 0.3rem; /* Mengurangi padding agar sel lebih kecil */
            text-align: center;
            vertical-align: middle;
        }
        .table th { background-color: #f2f2f2; }
        h2, h4 { text-align: center; }
        .trip-cell { background-color: #d6eaf8; font-weight: bold; }
        .available-cell { background-color: #f8f9f9; }
    </style>
</head>
<body>

    <h2>Laporan Jadwal Driver</h2>
    <h4>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d F Y') }}</h4>

    @php
        $all_jam_with_minutes = [];
        for ($h = 5; $h <= 23; $h++) {
            $hour = str_pad($h, 2, '0', STR_PAD_LEFT);
            $all_jam_with_minutes[] = "$hour.00";
            $all_jam_with_minutes[] = "$hour.30";
        }
        $all_jam_with_minutes[] = '24.00';

        function findClosestTimeSlot($carbon, $allJam, $tripTime, $type) {
            if (!$tripTime) return null;
            $trip_time_obj = $carbon->parse($tripTime);
            $closest_slot = null;
            $smallest_diff = PHP_INT_MAX;

            foreach ($allJam as $slot) {
                $timeToParse = ($slot == '24.00') ? '23:59:59' : str_replace('.', ':', $slot);
                $slot_time_obj = $carbon->parse($timeToParse);
                $diff_seconds = $trip_time_obj->diffInSeconds($slot_time_obj, false);

                if ($type == 'start') {
                    if ($diff_seconds <= 0 && abs($diff_seconds) < $smallest_diff) {
                        $smallest_diff = abs($diff_seconds);
                        $closest_slot = $slot;
                    }
                } else { // 'end'
                    if ($diff_seconds >= 0 && $diff_seconds < $smallest_diff) {
                        $smallest_diff = $diff_seconds;
                        $closest_slot = $slot;
                    }
                }
            }
            return $closest_slot;
        }
    @endphp

    <table class="table">
        <thead>
            <tr>
                <th style="width: 60px;">Jam</th>
                @foreach ($drivers as $driver)
                    <th>{{ $driver->nama_driver }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($all_jam_with_minutes as $time_slot)
                <tr>
                    @if (str_ends_with($time_slot, '.00'))
                        <th rowspan="2">{{ $time_slot }}</th>
                    @endif

                    @foreach ($drivers as $driver)
                        @php
                            $trip_for_this_slot = null;
                            $rowspan = 1;
                            $is_covered = false;

                            foreach($driver->p_kendaraans as $trip) {
                                if ($trip->jam_berangkat && $trip->jam_kembali) {
                                    $start_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $trip->jam_berangkat, 'start');
                                    if ($time_slot == $start_slot) {
                                        $trip_for_this_slot = $trip;
                                        $end_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $trip->jam_kembali, 'end');
                                        $start_index = array_search($start_slot, $all_jam_with_minutes);
                                        $end_index = array_search($end_slot, $all_jam_with_minutes);
                                        $rowspan = ($end_index - $start_index) >= 1 ? ($end_index - $start_index) : 1;
                                        break;
                                    }
                                }
                            }

                            if (!$trip_for_this_slot) {
                                foreach ($driver->p_kendaraans as $trip) {
                                    if ($trip->jam_berangkat && $trip->jam_kembali) {
                                        $start_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $trip->jam_berangkat, 'start');
                                        $end_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $trip->jam_kembali, 'end');
                                        if ($time_slot > $start_slot && $time_slot < $end_slot) {
                                            $is_covered = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        @endphp

                        @if ($is_covered)
                            {{-- Jangan render sel apa pun --}}
                        @elseif ($trip_for_this_slot)
                            <td rowspan="{{ $rowspan }}" class="trip-cell">
                                {{ $trip_for_this_slot->tujuan }}
                                <br>
                                <small>(PIC: {{ $trip_for_this_slot->nama_pic }})</small>
                            </td>
                        @else
                            <td class="available-cell"></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
