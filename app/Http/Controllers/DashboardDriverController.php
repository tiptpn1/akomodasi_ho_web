<?php

namespace App\Http\Controllers;

use App\Models\MDriver;
use App\Models\PKendaraan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardDriverController extends Controller
{
    /**
     * Menampilkan halaman dasbor jadwal driver.
     */
    public function index()
    {
        return view('admin.dashboarddriver.index');
    }

    /**
     * Mengambil dan mengembalikan konten jadwal sebagai tabel HTML.
     */
    public function getContent(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Sesi Anda telah berakhir'], 419);
        }

        try {
            // --- PERBAIKAN: Menggunakan Carbon untuk konversi tanggal yang aman ---
            // Membuat objek Carbon dari format 'bulan/tanggal/tahun' dan mengubahnya
            // ke format 'tahun-bulan-tanggal' yang sesuai dengan database.
            $date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

        } catch (\Exception $e) {
            // Menangani jika format tanggal yang diterima tidak valid
            return response()->json(['message' => 'Format tanggal tidak valid.'], 400);
        }

        try {
            $drivers = MDriver::where('driver_regional_id', Auth::user()->bagian->regional->id_regional)
            ->with(['p_kendaraans' => function ($query) use ($date) {
                $query->where('tgl_berangkat', $date)
                ->where('status', 2)
                      ->orderBy('jam_berangkat', 'asc');
            }])
            ->get();

            $data = [
                'drivers' => $drivers,
                'carbon' => new Carbon(),
            ];

            return view('admin.dashboarddriver.table_schedule', $data);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error', 'message' => $th->getMessage(),
                'line' => $th->getLine(), 'file' => $th->getFile(),
            ], 400);
        }
    }

    /**
     * Menangani permintaan untuk menampilkan detail perjalanan tertentu.
     */
    public function show_trip($id)
    {
        $trip = PKendaraan::with(['driverDetail', 'kendaraanDetail'])->find($id);

        if (!$trip) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json(['data' => $trip]);
    }


    /**
     * Ekspor jadwal driver harian penuh ke file PDF.
     */
    public function export_pdf(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Sesi Anda telah berakhir'], 419);
        }
        
        try {
            // --- PERBAIKAN: Menggunakan Carbon untuk konversi tanggal yang aman ---
            $date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Format tanggal tidak valid.'], 400);
        }

        try {
            $drivers = MDriver::where('driver_regional_id', Auth::user()->bagian->regional->id_regional)
            ->with(['p_kendaraans' => function ($query) use ($date) {
                $query->where('tgl_berangkat', $date)
                ->where('status', 2)
                      ->orderBy('jam_berangkat', 'asc');
            }])
            ->get();
            
            $data = [
                'drivers' => $drivers,
                'date' => $date, // Mengirim tanggal yang sudah diformat
                'carbon' => new Carbon(),
            ];

            $pdf = Pdf::loadView('admin.dashboarddriver.export_pdf', $data)
                      ->setPaper('A4', 'landscape');
            
            return $pdf->stream('Laporan Jadwal Driver ' . $date . '_' . time() . '.pdf');

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error', 'message' => $th->getMessage(),
                'line' => $th->getLine(), 'file' => $th->getFile(),
            ], 400);
        }
    }
}
