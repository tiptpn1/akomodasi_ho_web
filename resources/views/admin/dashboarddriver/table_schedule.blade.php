@php
    // Berkas ini adalah view parsial yang dimuat melalui AJAX.

    // Array slot waktu untuk grid jadwal, dari jam 05:00 hingga 24:00 dengan interval 30 menit.
    $all_jam_with_minutes = [];
    for ($h = 5; $h <= 23; $h++) {
        $hour = str_pad($h, 2, '0', STR_PAD_LEFT);
        $all_jam_with_minutes[] = "$hour.00";
        $all_jam_with_minutes[] = "$hour.30";
    }
    $all_jam_with_minutes[] = '24.00';

    /**
     * Fungsi bantuan untuk menyelaraskan waktu perjalanan ke grid 30 menit.
     */
    function findClosestTimeSlot($carbon, $allJam, $tripTime, $type) {
        if (!$tripTime) return null;

        try {
            $trip_time_obj = $carbon->parse($tripTime);
        } catch (\Exception $e) {
            return $type == 'start' ? '05.00' : '24.00';
        }
        
        $prev_slot = null;

        foreach ($allJam as $slot) {
            $slot_time_obj = $carbon->parse(str_replace('.', ':', $slot));

            if ($type == 'start') {
                // Untuk start time, cari slot terdekat yang sama atau di bawah waktu trip
                if ($slot_time_obj->greaterThan($trip_time_obj)) {
                    // Jika slot saat ini sudah melewati waktu trip, kembalikan slot sebelumnya
                    return $prev_slot ?? $slot; 
                }
            } else { // 'end'
                // Untuk end time, cari slot terdekat yang sama atau di atas waktu trip
                if ($slot_time_obj->gte($trip_time_obj)) { // <-- PERBAIKAN DI SINI
                    return $slot;
                }
            }
            $prev_slot = $slot;
        }

        return end($allJam);
    }
@endphp

{{-- Kontainer untuk tabel jadwal, memungkinkan scroll horizontal. --}}
<div class="table-overflow-x">
    <table class="table table-bordered" id="tableSchedule">
        <thead>
            <tr>
                <th scope="col" style="width: 80px; min-width: 80px;">Jam</th>
                @foreach ($drivers as $driver)
                    @php
                        // Menggunakan data_get untuk akses aman (penting untuk driver dinamis)
                        $is_online = data_get($driver, 'is_online', false);
                        $is_rental = data_get($driver, 'is_rental', false);
                        $nama_driver = data_get($driver, 'nama_driver', 'Driver N/A');
                        
                        $header_class = '';
                        $header_text = $nama_driver;
                        
                        if ($is_online || $is_rental) {
                            if ($is_online) {
                                $header_text = 'Grab'; // Sesuai permintaan
                            } elseif ($is_rental) {
                                // AMBIL DATA DARI SUMBER YANG BENAR: $driver['trips'][0]
                                // Ini adalah trip pertama untuk kolom rental ini.
                                $rental_trip = data_get($driver, 'trips.0');
                                $rental_driver_name = data_get($rental_trip, 'rental_driver', 'Rental');
                                $rental_nopol = data_get($rental_trip, 'kendaraanDetail.nopol') ?? data_get($rental_trip, 'rental_kendaraan', 'N/A');
                                $header_text = "$rental_driver_name ($rental_nopol)";
                            }
                        }
                    @endphp
                    <th scope="col" class="{{ $header_class }}" style="min-width: 150px;">{{ $header_text }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($all_jam_with_minutes as $time_slot)
                <tr>
                    {{-- Tampilkan slot waktu di kolom pertama --}}
                    @if (str_ends_with($time_slot, '.00') && $time_slot != '24.00')
                        <th scope="row" rowspan="2">{{ str_replace('.', ':', $time_slot) }}</th>
                    @elseif ($time_slot == '24.00')
                         @continue
                    @endif
                    
                    @foreach ($drivers as $driver)
                        @php
                            // Mengakses perjalanan dengan aman
                            $trips = data_get($driver, 'trips'); 
                            if (!is_array($trips) && !($trips instanceof \Illuminate\Database\Eloquent\Collection)) {
                                $trips = [];
                            }
                            
                            $trip_for_this_slot = null;
                            $rowspan = 1;
                            $is_covered = false;

                            // Cek apakah ada perjalanan yang DIMULAI pada slot waktu ini
                            foreach($trips as $trip) {
                                $jam_berangkat = data_get($trip, 'jam_berangkat');
                                $jam_kembali = data_get($trip, 'jam_kembali');

                                if ($jam_berangkat && $jam_kembali) {
                                    $start_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $jam_berangkat, 'start');
                                    
                                    if ($time_slot == $start_slot) {
                                        $trip_for_this_slot = $trip;
                                        // Hitung rowspan
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

                            // Jika tidak ada perjalanan yang dimulai, cek apakah slot ini TERTUTUP
                            if (!$trip_for_this_slot) {
                                foreach ($trips as $trip) {
                                    $jam_berangkat = data_get($trip, 'jam_berangkat');
                                    $jam_kembali = data_get($trip, 'jam_kembali');
                                    
                                    if ($jam_berangkat && $jam_kembali) {
                                        $start_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $jam_berangkat, 'start');
                                        $end_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $jam_kembali, 'end');
                                        
                                        // Cek apakah waktu slot saat ini berada di antara start dan end
                                        if ($time_slot > $start_slot && $time_slot < $end_slot) {
                                            $is_covered = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        @endphp

                        @if ($is_covered)
                            {{-- Jangan render sel karena sudah ditutupi oleh rowspan --}}
                        @elseif ($trip_for_this_slot)
                            @php
                                // Akses data trip dengan aman
                                $trip_id = data_get($trip_for_this_slot, 'id');
                                $tujuan = data_get($trip_for_this_slot, 'tujuan');
                                $nama_pic = data_get($trip_for_this_slot, 'nama_pic');
                                $status = data_get($trip_for_this_slot, 'status');
                                $nopol_kendaraan = data_get($trip_for_this_slot, 'kendaraanDetail.nopol') ?? data_get($trip_for_this_slot, 'rental_kendaraan');

                                $cell_class = 'trip-cell hover-pointer';
                                if (data_get($driver, 'is_online')) {
                                    $cell_class = 'trip-cell hover-pointer bg-info';
                                } elseif (data_get($driver, 'is_rental')) {
                                    $cell_class = 'trip-cell hover-pointer bg-warning text-dark';
                                }
                            @endphp
                            {{-- Render sel perjalanan dengan rowspan --}}
                            <td rowspan="{{ $rowspan }}" class="{{ $cell_class }}" onclick="detail('{{ $trip_id }}')">
                                <div style="font-weight: bold; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $tujuan }}
                                </div>
                                <div>PIC: {{ $nama_pic }}</div>
                                <div class="text-right mt-1">
                                    <i class="fas fa-2x {{ $status == 2? 'fa-check-circle' : 'fa-clock' }}" style="color: {{ $status == 2? 'white' : 'black' }}"></i>
                                </div>
                            </td>
                        @else
                            {{-- Render sel kosong --}}
                            <td class="available-cell"></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>