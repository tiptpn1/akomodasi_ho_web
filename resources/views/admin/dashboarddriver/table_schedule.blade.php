@php
    // Berkas ini adalah view parsial yang dimuat melalui AJAX.

    // Array slot waktu untuk grid jadwal, dari jam 07:00 hingga 21:00 dengan interval 30 menit.
    $all_jam_with_minutes = [];
    for ($h = 5; $h <= 23; $h++) {
        $hour = str_pad($h, 2, '0', STR_PAD_LEFT);
        $all_jam_with_minutes[] = "$hour.00";
        $all_jam_with_minutes[] = "$hour.30";
    }
    $all_jam_with_minutes[] = '24.00';

    /**
     * Fungsi bantuan untuk menemukan slot waktu terdekat di grid untuk waktu perjalanan tertentu.
     * Ini penting untuk menyelaraskan waktu mulai/selesai perjalanan ke grid 30 menit.
     * @param \Carbon\Carbon $carbon - Instance Carbon untuk mem-parsing waktu.
     * @param array $allJam - Array yang berisi semua slot waktu.
     * @param string|null $tripTime - Waktu perjalanan dari database (misal: "09:15:00").
     * @param string $type - Tipe pembulatan, 'start' (ke bawah) atau 'end' (ke atas).
     * @return string|null - Slot waktu yang sesuai dari grid (misal: "09.00").
     */
    function findClosestTimeSlot($carbon, $allJam, $tripTime, $type) {
        if (!$tripTime) return null;
        $trip_time_obj = $carbon->parse($tripTime);
        $closest_slot = null;
        $smallest_diff = PHP_INT_MAX;

        foreach ($allJam as $slot) {
            $slot_time_obj = $carbon->parse(str_replace('.', ':', $slot));
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

{{-- Kontainer untuk tabel jadwal, memungkinkan scroll horizontal. --}}
<div class="table-overflow-x">
    <table class="table table-bordered" id="tableSchedule">
        <thead>
            <tr>
                <th scope="col" style="width: 80px; min-width: 80px;">Jam</th>
                @foreach ($drivers as $driver)
                    <th scope="col" style="min-width: 150px;">{{ $driver->nama_driver }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($all_jam_with_minutes as $time_slot)
                <tr>
                    {{-- Tampilkan slot waktu di kolom pertama, gabungkan baris untuk jam penuh --}}
                    @if (str_ends_with($time_slot, '.00'))
                        <th scope="row" rowspan="2">{{ $time_slot }}</th>
                    @endif
                    
                    @foreach ($drivers as $driver)
                        @php
                            $trip_for_this_slot = null;
                            $rowspan = 1;
                            $is_covered = false;

                            // Cek apakah ada perjalanan yang DIMULAI pada slot waktu ini
                            foreach($driver->p_kendaraans as $trip) {
                                // Pastikan data waktu ada sebelum diproses
                                if ($trip->jam_berangkat && $trip->jam_kembali) {
                                    $start_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $trip->jam_berangkat, 'start');
                                    if ($time_slot == $start_slot) {
                                        $trip_for_this_slot = $trip;
                                        // Hitung rowspan
                                        $end_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $trip->jam_kembali, 'end');
                                        $start_index = array_search($start_slot, $all_jam_with_minutes);
                                        $end_index = array_search($end_slot, $all_jam_with_minutes);
                                        // Rowspan adalah selisih indeks, minimal 1
                                        $rowspan = ($end_index - $start_index) >= 1 ? ($end_index - $start_index) : 1;
                                        break;
                                    }
                                }
                            }

                            // Jika tidak ada perjalanan yang dimulai, cek apakah slot ini TERTUTUP oleh perjalanan sebelumnya
                            if (!$trip_for_this_slot) {
                                foreach ($driver->p_kendaraans as $trip) {
                                    if ($trip->jam_berangkat && $trip->jam_kembali) {
                                        $start_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $trip->jam_berangkat, 'start');
                                        $end_slot = findClosestTimeSlot($carbon, $all_jam_with_minutes, $trip->jam_kembali, 'end');
                                        // Jika slot waktu saat ini berada di antara slot mulai dan akhir
                                        if ($time_slot > $start_slot && $time_slot < $end_slot) {
                                            $is_covered = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        @endphp

                        @if ($is_covered)
                            {{-- Jangan render sel apa pun karena sudah ditutupi oleh rowspan --}}
                        @elseif ($trip_for_this_slot)
                            {{-- Render sel perjalanan dengan rowspan yang sudah dihitung --}}
                            <td rowspan="{{ $rowspan }}" class="trip-cell hover-pointer" onclick="detail('{{ $trip_for_this_slot->id }}')">
                                <div style="font-weight: bold; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $trip_for_this_slot->tujuan }}
                                </div>
                                <div>PIC: {{ $trip_for_this_slot->nama_pic }}</div>
                                <div class="text-right mt-1">
                                <i class="fas fa-2x {{ $trip_for_this_slot->status == 2? 'fa-check-circle' : 'fa-clock' }}" style="color: {{ $trip_for_this_slot->status == 2? 'blue' : 'black' }}"></i>
                                    
                                </div>
                            </td>
                        @else
                            {{-- Render sel kosong yang menandakan driver tersedia --}}
                            <td class="available-cell"></td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
