<?php

namespace App\Http\Controllers;

use App\Models\MDriver;
use App\Models\PKendaraan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        DB::enableQueryLog(); // <-- AKTIFKAN LOG

        try {
            // Konversi tanggal dari format 'm/d/Y' (datepicker JS) ke 'Y-m-d' (DB)
            $date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json(['message' => "Format tanggal tidak valid: '{$request->date}'. Harap gunakan format MM/DD/YYYY."], 400);
        }

        try {
            // 1. Ambil Driver Reguler (Selain ID 99)
            $allDrivers = MDriver::where('id_driver', '!=', 99)->get();
            $grabDriver = MDriver::find(99);

            // 2. Ambil SEMUA perjalanan pada tanggal yang dipilih dalam satu query
            $allTripsOnDate = PKendaraan::where('tgl_berangkat', $date)
                ->with(['kendaraanDetail', 'driverDetail']) // Eager load relasi yang dibutuhkan
                ->orderBy('jam_berangkat', 'asc')
                ->get();

            // 3. Siapkan struktur data final, dimulai dengan driver reguler
            $finalDrivers = [];
            foreach ($allDrivers as $driver) {
                $finalDrivers[$driver->id_driver] = [
                    'nama_driver' => $driver->nama_driver,
                    'is_online' => false,
                    'is_rental' => false,
                    'trips' => [], // Awalnya kosong
                ];
            }

            // 4. SELALU tambahkan "Grab" dan "Rental" sebagai kolom statis
            if ($grabDriver) {
                $finalDrivers[99] = ['nama_driver' => 'Grab', 'is_online' => true, 'is_rental' => false, 'trips' => []];
            }
            $finalDrivers['rental'] = ['nama_driver' => 'Rental', 'is_online' => false, 'is_rental' => true, 'trips' => []];

            // 5. Distribusikan perjalanan yang ditemukan ke "ember" driver yang sesuai
            foreach ($allTripsOnDate as $trip) {
                // Cek apakah ini perjalanan rental
                $isRentalTrip = $trip->rental_driver && ($trip->driver == null || $trip->driver == 0);

                if ($isRentalTrip) {
                    // Masukkan ke ember "Rental"
                    $finalDrivers['rental']['trips'][] = $trip;
                } elseif ($trip->driver == 99 && isset($finalDrivers[99])) {
                    // Masukkan ke ember "Grab"
                    $finalDrivers[99]['trips'][] = $trip;
                } elseif (isset($finalDrivers[$trip->driver])) {
                    // Masukkan ke ember driver reguler
                    $finalDrivers[$trip->driver]['trips'][] = $trip;
                }
            }

            // Konversi ke array non-asosiatif agar bisa di-looping di view
            $finalDrivers = array_values($finalDrivers);

            $data = [
                'drivers' => $finalDrivers,
                'carbon' => new Carbon(),
            ];

            // dd($data); // Untuk debug jika perlu

            // KEMBALIKAN KE SEMULA: Render view dan kirim sebagai HTML
            return view('admin.dashboarddriver.table_schedule', $data);

        } catch (\Throwable $th) {
            // Detail error untuk debug 500
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
    // public function export_pdf(Request $request)
    // {
    //     if (!auth()->check()) {
    //         return response()->json(['message' => 'Sesi Anda telah berakhir'], 419);
    //     }
        
    //     try {
    //         $date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');
    //     } catch (\Exception $e) {
    //         return response()->json(['message' => 'Format tanggal tidak valid.'], 400);
    //     }

    //     try {
    //         // 1. Ambil Driver Reguler (Selain ID 99)
    //         $regularDrivers = MDriver::where('driver_regional_id', Auth::user()->bagian->regional->id_regional)
    //         ->where('id_driver', '!=', 99) 
    //         ->with(['p_kendaraans' => function ($query) use ($date) {
    //             $query->where('tgl_berangkat', $date)
    //             //->where('status', 2)
    //             ->orderBy('jam_berangkat', 'asc');
    //         }])
    //         ->get();

    //         // 2. Ambil Perjalanan untuk Driver Online (ID 99)
    //         $onlineTrips = PKendaraan::where('tgl_berangkat', $date)
    //             //->where('status', 2)
    //             ->where('driver', 99)
    //             ->with('kendaraanDetail')
    //             ->orderBy('jam_berangkat', 'asc')
    //             ->get();
                
    //         // 3. Ambil Perjalanan untuk Rental Driver
    //         $rentalTrips = PKendaraan::where('tgl_berangkat', $date)
    //             //->where('status', 2)
    //             ->where(function ($query) {
    //                 $query->whereNull('driver')
    //                       ->orWhere('driver', 0);
    //             })
    //             ->whereNotNull('rental_driver')
    //             ->orderBy('jam_berangkat', 'asc')
    //             ->get();


    //         // Proses Driver Online dan Rental Driver menjadi kolom dinamis (setiap trip = 1 kolom)
    //         $dynamicDrivers = [];
            
    //         // Memproses Driver Online (ID 99)
    //         foreach ($onlineTrips as $index => $trip) {
    //             $driverName = 'Online Driver ' . ($index + 1);
    //             $dynamicDrivers[] = [
    //                 'nama_driver' => $driverName,
    //                 'is_online' => true,
    //                 'is_rental' => false,
    //                 'trips' => [$trip],
    //                 'original_trip_id' => $trip->id,
    //             ];
    //         }
            
    //         // Memproses Rental Driver
    //         foreach ($rentalTrips as $index => $trip) {
    //              $driverName = $trip->rental_driver;
    //              $dynamicDrivers[] = [
    //                  'nama_driver' => "Rental ({$driverName}) " . ($index + 1),
    //                  'is_online' => false,
    //                  'is_rental' => true,
    //                  'trips' => [$trip],
    //                  'original_trip_id' => $trip->id,
    //              ];
    //         }

    //         // Gabungkan semua driver: Reguler + Dinamis (Online/Rental)
    //         $allDrivers = $regularDrivers->toArray();
            
    //         // Konversi koleksi driver reguler agar formatnya sama dengan dynamicDrivers
    //         foreach ($allDrivers as $key => $driver) {
    //             $allDrivers[$key]['is_online'] = false;
    //             $allDrivers[$key]['is_rental'] = false;
    //             $allDrivers[$key]['trips'] = $driver['p_kendaraans'];
    //         }

    //         $finalDrivers = array_merge($allDrivers, $dynamicDrivers);
 
    //         $data = [
    //             'drivers' => $finalDrivers,
    //             'date' => $date, // Mengirim tanggal yang sudah diformat
    //             'carbon' => new Carbon(),
    //         ];

    //         $pdf = Pdf::loadView('admin.dashboarddriver.export_pdf', $data)
    //                   ->setPaper('A4', 'landscape');
            
    //         return $pdf->stream('Laporan Jadwal Driver ' . $date . '_' . time() . '.pdf');

    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => 'error', 'message' => $th->getMessage(),
    //             'line' => $th->getLine(), 'file' => $th->getFile(),
    //         ], 400);
    //     }
    // }


public function export_pdf(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Sesi Anda telah berakhir'], 419);
        }
        
        $date = $request->date;
        $db_date = null;

        $common_formats = ['m/d/Y', 'd/m/Y', 'Y-m-d']; 
        $clean_date = trim($date);
        
        // Coba parsing tanggal
        foreach ($common_formats as $format) {
            try {
                $db_date = Carbon::createFromFormat($format, $clean_date)->format('Y-m-d');
                break;
            } catch (\Exception $e) {
                continue; 
            }
        }
        
        if (is_null($db_date)) {
            return response()->json([
                'message' => "Format tanggal '{$date}' tidak valid. Harap gunakan format seperti MM/DD/YYYY atau DD/MM/YYYY."
            ], 400);
        }
        $date = $db_date; 

        try {
            // 1. Ambil Driver Reguler (Selain ID 99)
            //$regularDrivers = MDriver::where('driver_regional_id', Auth::user()->bagian->regional->id_regional)
            $regularDrivers = MDriver::where('id_driver', '!=', 99) 
            ->with(['p_kendaraans' => function ($query) use ($date) {
                $query->where('tgl_berangkat', $date)
                //->where('status', 2)
                ->orderBy('jam_berangkat', 'asc');
            }])
            ->get();

            // 2. Ambil Perjalanan untuk Driver Online (ID 99)
            $onlineTrips = PKendaraan::where('tgl_berangkat', $date)
                //->where('status', 2)
                ->where('driver', 99)
                ->with('kendaraanDetail')
                ->orderBy('jam_berangkat', 'asc')
                ->get();
                
            // 3. Ambil Perjalanan untuk Rental Driver
            $rentalTrips = PKendaraan::where('tgl_berangkat', $date)
                //->where('status', 2)
                ->where(function ($query) {
                    $query->whereNull('driver')
                          ->orWhere('driver', 0);
                })
                ->whereNotNull('rental_driver')
                ->orderBy('jam_berangkat', 'asc')
                ->get();


            // --- PROSES INJEKSI DUMMY KENDARAAN ---
            foreach ($onlineTrips as $trip) {
                if (!$trip->kendaraanDetail) {
                    $nopol = $trip->no_polisi ?? ($trip->rental_kendaraan ?? 'ONLINE-N/A');
                    $trip->kendaraanDetail = (object)['nopol' => $nopol, 'foto' => null];
                }
            }
            foreach ($rentalTrips as $trip) {
                if (!$trip->kendaraanDetail) {
                    $nopol = $trip->rental_kendaraan ?? 'RENTAL-N/A';
                    $trip->kendaraanDetail = (object)['nopol' => $nopol, 'foto' => null];
                }
            }


            // Proses Driver Online dan Rental Driver menjadi kolom dinamis
            $dynamicDrivers = [];
            
            foreach ($onlineTrips as $index => $trip) {
                $driverName = 'Online Driver ' . ($index + 1);
                $dynamicDrivers[] = [
                    'nama_driver' => $driverName,
                    'is_online' => true,
                    'is_rental' => false,
                    'trips' => [$trip],
                    'original_trip_id' => $trip->id,
                ];
            }
            
            foreach ($rentalTrips as $index => $trip) {
                 $driverName = $trip->rental_driver;
                 $dynamicDrivers[] = [
                     'nama_driver' => "Rental ({$driverName}) " . ($index + 1),
                     'is_online' => false,
                     'is_rental' => true,
                     'trips' => [$trip],
                     'original_trip_id' => $trip->id,
                 ];
            }

            // Gabungkan semua driver
            $allDrivers = $regularDrivers->toArray();
            
            foreach ($allDrivers as $key => $driver) {
                $allDrivers[$key]['is_online'] = false;
                $allDrivers[$key]['is_rental'] = false;
                $allDrivers[$key]['trips'] = $driver['p_kendaraans'];
            }

            $finalDrivers = array_merge($allDrivers, $dynamicDrivers);
 
            $data = [
                'drivers' => $finalDrivers,
                'date' => $db_date,
                'carbon' => new Carbon(),
            ];

            $pdf = Pdf::loadView('admin.dashboarddriver.export_pdf', $data)
                      ->setPaper('A4', 'landscape');
            
            return $pdf->stream('Laporan Jadwal Driver ' . $db_date . '_' . time() . '.pdf');

        } catch (\Throwable $th) {
            // Mengembalikan 500 jika terjadi crash internal setelah parsing tanggal sukses
            return response()->json([
                'status' => 'error', 
                'message' => 'Terjadi kesalahan internal saat memproses PDF: ' . $th->getMessage(),
                'line' => $th->getLine(), 'file' => $th->getFile(),
            ], 500); 
        }
    }
    
}