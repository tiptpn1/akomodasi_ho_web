<?php

namespace App\View\Components\Partials;

use App\Models\AgendaKendaraan;
use App\Models\SendVicon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Navbar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $daftar_hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        $user = Auth::user();
        return view('components.partials.navbar', compact('daftar_hari', 'user'));
    }

    public function listNotifikasiPetugasTI($username)
    {
        $sendvicons  = SendVicon::all();
        $idSendvicon = [];

        foreach ($sendvicons as $sendvicon) {
            $petugas = explode(", ", $sendvicon->petugasti);
            $persiapan = explode(", ", $sendvicon->persiapanvicon);

            if (!empty($petugas)) {
                foreach ($petugas as $k => $value) {
                    // Jika user ada di kolom petugas dan tidak ada di kolom persiapan
                    if ($petugas[$k] == $username && !in_array($petugas[$k], $persiapan)) {
                        $idSendvicon[] = $sendvicon->id;
                    }
                }
            }
        }

        // Jika ada ID yang sesuai
        if (!empty($idSendvicon)) {
            $result = Sendvicon::with(['ruangan', 'masterLink', 'bagian']) // Menggunakan eager loading untuk relasi ruangan dan masterlink
                ->whereIn('id', $idSendvicon) // Ambil data berdasarkan ID
                ->orderBy('tanggal', 'desc')
                ->orderBy('waktu', 'asc')
                ->get();

            return $result;
        }

        return collect();
    }
    public function listNotifikasiPetugasRapat($username)
    {
        $sendvicons  = SendVicon::all();
        $idSendvicon = [];

        foreach ($sendvicons as $sendvicon) {
            $petugas = explode(", ", $sendvicon->petugasruangrapat);
            $persiapan = explode(", ", $sendvicon->persiapanrapat);

            if (!empty($petugas)) {
                foreach ($petugas as $k => $value) {
                    // Jika user ada di kolom petugas dan tidak ada di kolom persiapan
                    if ($petugas[$k] == $username && !in_array($petugas[$k], $persiapan)) {
                        $idSendvicon[] = $sendvicon->id;
                    }
                }
            }
        }

        // Jika ada ID yang sesuai
        if (!empty($idSendvicon)) {
            $result = Sendvicon::with(['ruangan', 'masterLink', 'bagian']) // Menggunakan eager loading untuk relasi ruangan dan masterlink
                ->whereIn('id', $idSendvicon) // Ambil data berdasarkan ID
                ->orderBy('tanggal', 'desc')
                ->orderBy('waktu', 'asc')
                ->get();

            return $result;
        }

        return collect();
    }
    public function listNotifikasiDriver($user_id)
    {
        $data = AgendaKendaraan::with(['kendaraan', 'user', 'bagian'])
            ->where('id_user', $user_id)
            ->where(function ($query) {
                $query->where('persiapan', '!=', 'ready')
                    ->orWhereNull('persiapan');
            })
            ->orderBy('tanggal', 'desc')
            ->get();
        return $data;
    }

    public function listViconToday()
    {
        $data = SendVicon::with(['ruangan', 'masterLink', 'bagian'])
            ->where('tanggal', '=', date('Y-m-d'))
            ->where('status', '!=', 'Expired')
            ->where('status', '!=', 'Cancel')
            ->orderBy('waktu', 'desc')
            ->get();

        return $data;
    }

    public function listAgendaKendaraanToday()
    {
        $data = AgendaKendaraan::with(['kendaraan', 'user', 'bagian'])
            ->where('tanggal', '=', date('Y-m-d'))
            ->get();

        return $data;
    }
}
