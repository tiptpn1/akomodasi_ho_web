<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Jadwal Driver</title>
    <style>
        /* Mengatur halaman agar tidak memiliki margin */
        @page { margin: 5px; }

        body {
            font-family: DejaVu Sans, sans-serif; /* Gunakan font yang mendukung DomPDF */
            font-size: 8px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 0.2rem;
            text-align: center;
            vertical-align: middle;
            line-height: 1; /* Mengurangi jarak antar baris */
        }
        .table th { background-color: #f2f2f2; }
        h2, h4 { text-align: center; }
        
        /* Gaya Cell Dinamis */
        .trip-cell { background-color: #3498db; color: white; font-weight: bold; }
        .bg-info { background-color: #17a2b8 !important; color: white; }
        .bg-warning { background-color: #ffc107 !important; color: black; }
        .available-cell { background-color: #f8f9f9; }
        .small-text { font-size: 8px; }
    </style>
</head>
<body>

    <h2>Laporan Jadwal Driver</h2>
    <h4>Tanggal: {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}</h4>

    @php
        // --- LOGIKA HELPER FUNCTION (DIKOREKSI) ---
        $all_jam_with_minutes = [];
        for ($h = 5; $h <= 23; $h++) {
            $hour = str_pad($h, 2, '0', STR_PAD_LEFT);
            $all_jam_with_minutes[] = "$hour.00";
            $all_jam_with_minutes[] = "$hour.30";
        }
        $all_jam_with_minutes[] = '24.00';

        function findClosestTimeSlot($carbon, $allJam, $tripTime, $type) {
            if (!$tripTime) return null;

            try {
                $trip_time_obj = $carbon->parse($tripTime);
            } catch (\Exception $e) {
                return $type == 'start' ? '05.00' : '24.00';
            }
            
            $prev_slot = null;

            foreach ($allJam as $slot) {
                // Menggunakan gte() dan greaterThan() yang kompatibel
                $slot_time_obj = $carbon->parse(str_replace('.', ':', $slot));

                if ($type == 'start') {
                    if ($slot_time_obj->greaterThan($trip_time_obj)) {
                        return $prev_slot ?? $slot; 
                    }
                } else { // 'end'
                    if ($slot_time_obj->gte($trip_time_obj)) {
                        return $slot;
                    }
                }
                $prev_slot = $slot;
            }

            return end($allJam);
        }
    @endphp

    <table class="table">
        <thead>
            <tr>
                <th style="width: 60px;">Jam</th>
                @foreach ($drivers as $driver)
                    @php
                        // --- PERBAIKAN FATAL ERROR: Akses data sebagai ARRAY (data_get) ---
                        $is_online = data_get($driver, 'is_online', false);
                        $is_rental = data_get($driver, 'is_rental', false);
                        
                        $header_class = '';
                        $header_text = data_get($driver, 'nama_driver', 'Driver N/A'); // Default Reguler Name
                        
                        if ($is_online || $is_rental) {
                            $trip = data_get($driver, 'trips.0');
                            $nopol = data_get($trip, 'kendaraanDetail.nopol') ?? data_get($trip, 'rental_kendaraan') ?? 'N/A';
                            
                            if ($is_online) {
                                $header_class = 'bg-info text-white';
                                $header_text = 'Online (' . $nopol . ')';
                            } elseif ($is_rental) {
                                $header_class = 'bg-warning text-dark';
                                $header_text = data_get($trip, 'rental_driver') . ' (' . $nopol . ')';
                            }
                        }
                    @endphp
                    <th>{{ $header_text }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($all_jam_with_minutes as $time_slot)
                <tr>
                    {{-- Tampilkan jam penuh dengan rowspan --}}
                    @if (str_ends_with($time_slot, '.00') && $time_slot != '24.00')
                        <th rowspan="2">{{ str_replace('.', ':', $time_slot) }}</th>
                    @elseif ($time_slot == '24.00')
                         @continue
                    @endif

                    @foreach ($drivers as $driver)
                        @php
                            // Mengambil trips dengan aman dari array/model
                            $trips = data_get($driver, 'trips'); 
                            if (!is_array($trips) && !($trips instanceof \Illuminate\Database\Eloquent\Collection)) {
                                $trips = [];
                            }

                            $trip_for_this_slot = null;
                            $rowspan = 1;
                            $is_covered = false;

                            // 1. Cek apakah trip DIMULAI di slot ini
                            foreach($trips as $trip) {
                                $jam_berangkat = data_get($trip, 'jam_berangkat');
                                $jam_kembali = data_get($trip, 'jam_kembali');

                                if ($jam_berangkat && $jam_kembali) {
                                    $start_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $jam_berangkat, 'start');
                                    
                                    if ($time_slot == $start_slot) {
                                        $trip_for_this_slot = $trip;
                                        $end_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $jam_kembali, 'end');
                                        $start_index = array_search($start_slot, $all_jam_with_minutes);
                                        $end_index = array_search($end_slot, $all_jam_with_minutes);
                                        $rowspan = ($end_index - $start_index) >= 1 ? ($end_index - $start_index) : 1;
                                        
                                        $current_index = array_search($time_slot, $all_jam_with_minutes);
                                        $max_rowspan = count($all_jam_with_minutes) - 1 - $current_index;
                                        $rowspan = min($rowspan, $max_rowspan);

                                        break;
                                    }
                                }
                            }

                            // 2. Cek apakah slot TERTUTUP oleh trip sebelumnya
                            if (!$trip_for_this_slot) {
                                foreach ($trips as $trip) {
                                    $jam_berangkat = data_get($trip, 'jam_berangkat');
                                    $jam_kembali = data_get($trip, 'jam_kembali');
                                    
                                    if ($jam_berangkat && $jam_kembali) {
                                        $start_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $jam_berangkat, 'start');
                                        $end_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $jam_kembali, 'end');
                                        
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
                            @php
                                // Ambil detail trip yang aman
                                $tujuan = data_get($trip_for_this_slot, 'tujuan');
                                $nama_pic = data_get($trip_for_this_slot, 'nama_pic');
                                $nopol_kendaraan = data_get($trip_for_this_slot, 'kendaraanDetail.nopol') ?? data_get($trip_for_this_slot, 'rental_kendaraan');

                                $cell_class = 'trip-cell';
                                if (data_get($driver, 'is_online')) {
                                    $cell_class = 'bg-info';
                                } elseif (data_get($driver, 'is_rental')) {
                                    $cell_class = 'bg-warning';
                                }
                            @endphp
                            <td rowspan="{{ $rowspan }}" class="{{ $cell_class }}">
                                <div>{{ $tujuan }}</div>
                                <div class="small-text">PIC: {{ $nama_pic }}</div>
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