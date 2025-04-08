<?php

namespace App\Http\Controllers;

use App\Models\JenisRapat;
use App\Models\Bagian;
use App\Models\Ruangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PKendaraan;
use App\Models\MDriver;

class DashboardDriverController extends Controller
{

    var $model;

    public function __construct()
    {
        $this->model = new PKendaraan();
    }


    public function index()
    {
        // Ambil bagian_reg berdasarkan master_bagian_id dari user yang login
        $bagian_reg = Bagian::where('master_bagian_id', Auth::user()->master_nama_bagian_id)
            ->orderBy('master_bagian_id', 'desc')
            ->first();

        // Pastikan $bagian_reg tidak null sebelum lanjut
        if (!$bagian_reg) {
            return back()->with('error', 'Data bagian regional tidak ditemukan.');
        }

        // // Ambil daftar lantai berdasarkan ruangan_regional_id
        // $list_lantai = Ruangan::select('lantai')
        //     ->where('ruangan_regional_id', $bagian_reg->bagian_regional_id)
        //     ->where('lantai', '!=', 0)
        //     ->distinct()
        //     ->get(); // Gunakan get() agar hasilnya koleksi model, bukan array

        $list_lantai =  MDriver::select('id_driver', 'nama_driver')->get();

        $data = [
            'list_lantai' => $list_lantai,
        ];

        return view('admin.dashboarddriver.index', $data);
    }


    public function getContent(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Your session has ended'], 419);
        }

        $lantai = $request->lt;
        $split_tanggal = explode('/', $request->date);
        $date = implode('-', [$split_tanggal[2], $split_tanggal[0], $split_tanggal[1]]);
        $jenis_rapat = PKendaraan::where('tgl_berangkat', $tgl_berangkat)
            ->where('status', 2)
            ->with(['driverDetail:id_driver,nama_driver,no_hp']) // Ambil hanya kolom tertentu
            ->get(['driver', 'jam_berangkat', 'jam_kembali']);

        try {
            $ruangan = $this->model->getSpesificData(array('driver' => $lantai), $date);

            $data = [
                'ruangan' => $ruangan,
                'jenis_rapat' => $jenis_rapat,
                'carbon' => new Carbon(),
            ];

            return view('admin.dashboarddriver.table_agenda', $data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 400);
        }
    }

    public function getDriverSchedule(Request $request)
    {
        $tgl_berangkat = $request->input('tgl_berangkat') ?: date('Y-m-d');

        // Ambil semua driver
        $drivers = MDriver::select('id_driver', 'nama_driver')->get();

        // List jam tetap
        $jamList = [
            '07:00',
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '12:00',
            '13:00',
            '14:00',
            '15:00',
            '16:00',
            '17:00',
            '18:00',
            '19:00',
            '20:00',
            '21:00'
        ];

        // Ambil data jadwal yang sedang digunakan
        $jadwal = PKendaraan::where('tgl_berangkat', $tgl_berangkat)
            ->where('status', 2)
            ->with(['driverDetail:id_driver,nama_driver,no_hp']) // Ambil hanya kolom tertentu
            ->get(['driver', 'jam_berangkat', 'jam_kembali']);




        // Susun data jadwal driver dalam format tabel
        $schedule = [];

        foreach ($jamList as $jam) {
            $row = ['jam' => $jam];

            // Inisialisasi key driver sebagai string di $row
            foreach ($drivers as $driver) {
                $row[(string) $driver->id_driver] = null; // Pakai string agar seragam
            }



            foreach ($jadwal as $j) {
                $driverId = (string) $j->driver; // Ubah ke string agar sesuai dengan key di $row



                if (array_key_exists($driverId, $row)) {
                    $jamBerangkat = strtotime($j->jam_berangkat);
                    $jamKembali = strtotime($j->jam_kembali);
                    $jamSekarang = strtotime($jam);

                    if ($jamSekarang >= $jamBerangkat && $jamSekarang < $jamKembali) {
                        $durasiTotal = $jamKembali - $jamBerangkat;
                        $durasiSekarang = $jamSekarang - $jamBerangkat;
                        $progress = $durasiTotal > 0 ? $durasiSekarang / $durasiTotal : 0;

                        $row[$driverId] = [
                            'text' => substr($j->driverDetail->nama_driver, 0, 20) . '...',
                            'fullText' => "Nama: " . $j->driverDetail->nama_driver . "<br>No HP: " . $j->driverDetail->no_hp,
                            'progress' => $progress
                        ];
                    }
                } else {
                }
            }



            $schedule[] = $row;
        }

        return response()->json([
            'status' => 'success',
            'drivers' => $drivers,  // Tambahkan daftar driver
            'schedule' => $schedule  // Tambahkan daftar jadwal dalam format yang benar
        ]);
    }


    public function export_pdf(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Your session has ended'], 419);
        }

        $split_tanggal = explode('/', $request->date);
        $date = implode('-', [$split_tanggal[2], $split_tanggal[0], $split_tanggal[1]]);
        $jenis_rapat = JenisRapat::all();

        try {
            $all_ruangan = [];
            $all_lantai = $this->model->getDataDistinct('lantai')->pluck('lantai');

            foreach ($all_lantai as $lantai) {
                $all_ruangan[] = $this->model->getSpesificData(array('lantai' => $lantai), $date);
            }

            $data = [
                'all_ruangan' => $all_ruangan,
                'date' => $date,
                'all_lantai' => $all_lantai,
                'jenis_rapat' => $jenis_rapat,
                'carbon' => new Carbon(),
            ];

            $pdf = Pdf::loadView('admin.dashboarddriver.export_pdf', $data)
                ->setPaper('A4', 'landscape');

            return $pdf->stream('Laporan Agenda pada Tanggal ' . $date  . '_' . time() . '.pdf');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 400);
        }
    }
}
