<?php

namespace App\Helpers;

use Carbon\Carbon;

class FunctionHelper
{
    public static function countDuration($start, $end)
    {
        // Mengonversi kembali ke objek Carbon untuk menghitung selisih
        $startTime = Carbon::createFromFormat('H:i', $start);
        $afterTime = Carbon::createFromFormat('H:i', $end);
        // Hitung selisih dalam menit
        $diffInMinutes = $startTime->diffInMinutes($afterTime);

        // Konversi menit ke format H:i
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;

        $duration = sprintf('%02d:%02d', $hours, $minutes);

        return $duration;
    }
}
