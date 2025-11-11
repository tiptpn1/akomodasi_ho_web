<?php

namespace App\Http\Controllers;

use App\Exports\PKendaraanExport;
use App\Models\Bagian;
use Illuminate\Http\Request;
use App\Models\SendVicon;
use App\Models\PKendaraan;
use App\Models\MDriver;
use App\Models\MKendaraan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class PKendaraanController extends Controller
{


    public function index(Request $request)
{
    $bagian = Auth::user()->master_nama_bagian_id;
    $divisi = Bagian::find($bagian);

    // Query dasar
    if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga'])) {
        $query = PKendaraan::with(['driverDetail', 'kendaraanDetail']);
    } else {
        $query = PKendaraan::with(['driverDetail', 'kendaraanDetail'])
            ->where('divisi', $divisi->master_bagian_nama);
    }

    // 🔎 Tambahkan filter dari form modal
    if ($request->filled('tgl_awal') && $request->filled('tgl_akhir')) {
        $query->whereBetween('tgl_berangkat', [$request->tgl_awal, $request->tgl_akhir]);
    } elseif ($request->filled('tgl_awal')) {
        $query->whereDate('tgl_berangkat', '>=', $request->tgl_awal);
    } elseif ($request->filled('tgl_akhir')) {
        $query->whereDate('tgl_berangkat', '<=', $request->tgl_akhir);
    }


    if ($request->filled('id_divisi') && $request->id_divisi !== 'all') {
        $query->where('divisi', $request->id_divisi);
    }

    if ($request->filled('jenis_tujuan')) {
        $query->where('jenis_tujuan', $request->jenis_tujuan);
    }

    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    // Ambil data sesuai filter
    $pkendaraan = $query->get();

    $get_divisi = Bagian::get();
    $get_drivers = MDriver::where('driver_regional_id', Auth::user()->bagian->regional->id_regional)->get();

    $view_data = [
        'pkendaraan'  => $pkendaraan,
        'divisi'      => $divisi->master_bagian_nama,
        'get_divisi'  => $get_divisi,
        'get_drivers' => $get_drivers,
    ];

    return view('pkendaraan.index', $view_data);
}



    public function getAvailableDrivers(Request $request)
    {
        $tgl_berangkat = $request->input('tgl_berangkat');
        $jam_berangkat = $request->input('jam_berangkat');
        $jam_kembali = $request->input('jam_kembali');

        // Pastikan input tidak kosong sebelum query
        if (!$tgl_berangkat || !$jam_berangkat || !$jam_kembali) {
            return response()->json(['drivers' => []]); // Return array kosong jika input tidak valid
        }

        // Format ulang tanggal agar sesuai dengan format MySQL (YYYY-MM-DD)
        $tgl_berangkat = \Carbon\Carbon::createFromFormat('d-m-Y', $tgl_berangkat)->format('Y-m-d');

        // Konversi format waktu ke MySQL (jika perlu)
        $jam_berangkat = date('H:i:s', strtotime($jam_berangkat));
        $jam_kembali = date('H:i:s', strtotime($jam_kembali));

        // Cari driver yang sedang digunakan dalam rentang waktu tersebut
        $driver_terpakai = PKendaraan::where('tgl_berangkat', $tgl_berangkat)
            ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
                $query->where(function ($q) use ($jam_berangkat, $jam_kembali) {
                    // Cek apakah jam_berangkat baru berada dalam rentang pemakaian driver yang sudah ada
                    $q->where('jam_berangkat', '<', $jam_kembali)
                        ->where('jam_kembali', '>', $jam_berangkat);
                });
            })
            ->where('status',2)
            ->pluck('driver') // Mengambil hanya kolom 'driver'
            ->toArray(); // Konversi ke array

        // // Ambil driver yang tidak sedang bertugas dalam waktu tersebut
        // $available_drivers = MDriver::whereNotIn('id_driver', $driver_terpakai)->get();

        // Inisialisasi query MDriver
    $query = MDriver::whereNotIn('id_driver', $driver_terpakai);
    $query->where('driver_regional_id', Auth::user()->bagian->regional->id_regional);

    // Eksekusi query untuk mendapatkan driver yang tersedia
    $available_drivers = $query->get();

        return response()->json(['drivers' => $available_drivers]);
    }

    public function getAvailableDriversAdmin(Request $request)
    {
        $tgl_berangkat = $request->input('tgl_berangkat');
        $jam_berangkat = $request->input('jam_berangkat');
        $jam_kembali = $request->input('jam_kembali');

        if (!$tgl_berangkat || !$jam_berangkat || !$jam_kembali) {
            return response()->json(['drivers' => [], 'vehicles' => []]);
        }

        $tgl_berangkat = \Carbon\Carbon::createFromFormat('d-m-Y', $tgl_berangkat)->format('Y-m-d');
        $jam_berangkat = date('H:i:s', strtotime($jam_berangkat));
        $jam_kembali = date('H:i:s', strtotime($jam_kembali));

        // Cari driver yang sedang digunakan dalam rentang waktu tersebut dan memiliki status = 2
        $driver_terpakai = PKendaraan::where('tgl_berangkat', $tgl_berangkat)
            ->where('status', 2)
            ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
                $query->where('jam_berangkat', '<', $jam_kembali)
                    ->where('jam_kembali', '>', $jam_berangkat);
            })
            ->pluck('driver')
            ->filter() // Menghapus nilai null
            ->toArray();

        // Ambil driver yang tidak sedang bertugas dalam waktu tersebut
        $available_drivers = MDriver::whereNotIn('id_driver', $driver_terpakai ?: [])->get();

        // Cari kendaraan yang sedang digunakan dalam rentang waktu tersebut dan memiliki status = 2
        $kendaraan_terpakai = PKendaraan::where('tgl_berangkat', $tgl_berangkat)
            ->where('status', 2)
            ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
                $query->where('jam_berangkat', '<', $jam_kembali)
                    ->where('jam_kembali', '>', $jam_berangkat);
            })
            ->pluck('no_polisi')
            ->filter() // Menghapus nilai null
            ->toArray();

        // Ambil kendaraan yang tidak sedang bertugas dalam waktu tersebut
        // $available_vehicles = MKendaraan::whereNotIn('id_kendaraan', $kendaraan_terpakai ?: [])->get();

        $query = MKendaraan::whereNotIn('id_kendaraan', $kendaraan_terpakai ?: []);
        $query->where('kendaraan_regional_id', Auth::user()->bagian->regional->id_regional);
        $available_vehicles = $query->get();

        return response()->json([
            'drivers' => $available_drivers,
            'vehicles' => $available_vehicles
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'file_memo' => 'mimes:pdf,jpg,jpeg,png|max:2048', // Maksimal 2MB
        ]);

        $pkendaraan = new PKendaraan();
        $pkendaraan->divisi = $request->divisi;
        $pkendaraan->kendaraan_regional_id = Auth::user()->bagian->regional->id_regional;
        $pkendaraan->nama_pic = $request->nama_pic;
        $pkendaraan->tgl_berangkat = Carbon::createFromFormat('d-m-Y', $request->tgl_berangkat)->format('Y-m-d');
        $pkendaraan->jenis_tujuan = $request->jenis_tujuan;
        $pkendaraan->jam_berangkat = $request->jam_berangkat;
        $pkendaraan->jam_kembali = $request->jam_kembali;
        $pkendaraan->tujuan = $request->tujuan;
        $pkendaraan->pejemputan = $request->pejemputan;
         $pkendaraan->no_wa = $request->no_wa;
        $pkendaraan->ket = $request->ket;

        // Jika driver yang dipilih adalah "Rental"
        if ($request->driver === "rental") {
            $pkendaraan->driver = null; // Tidak menyimpan ID driver dari database
            $pkendaraan->rental_driver = $request->rental_driver;
            $pkendaraan->rental_kendaraan = $request->rental_kendaraan;
        } else if ($request->driver === 99){
            $pkendaraan->driver = 99;
        } else {
            $pkendaraan->driver = $request->driver;
        }

        if ($request->hasFile('file_memo')) {
            $file = $request->file('file_memo');
            $name = $file->getClientOriginalName();
            $namefile = time() . '_' . $name;
            $file->move(public_path('uploads/memo'), $namefile);
            $pkendaraan->file_memo = 'uploads/memo/' . $namefile; // Simpan path
        } else {
            $pkendaraan->file_memo = null; // atau kasih default string lain
        }

        $pkendaraan->status = 1;
        $pkendaraan->save();

        return redirect()->route('pkendaraan.index')->with('success', 'Data berhasil disimpan!');
    }

    public function edit($id)
    {
        $data = PKendaraan::findOrFail($id);
        $drivers = MDriver::all();
        $kendaraans = MKendaraan::all();

        return response()->json([
            'data' => [
                'id' => $data->id,
                'divisi' => $data->divisi,
                'nama_pic' => $data->nama_pic,
                'tgl_berangkat' => $data->tgl_berangkat,
                'jam_berangkat' => $data->jam_berangkat,
                'jam_kembali' => $data->jam_kembali,
                'jenis_tujuan' => $data->jenis_tujuan,
                'tujuan' => $data->tujuan,
                'ket' => $data->ket,
                'pejemputan' => $data->pejemputan,
                'no_wa' => $data->no_wa,
                'driver' => $data->driver ?? 'Rental', // Jika null, set default Rental
                'rental_driver' => $data->rental_driver ?? '',
                'rental_kendaraan' => $data->rental_kendaraan ?? '',
                'no_polisi' => ($data->driver === "Rental" || !$data->driver) ? null : $data->no_polisi
            ],
            'drivers' => $drivers,
            'kendaraans' => $kendaraans
        ]);
    }


    public function update(Request $request, $id)
    {
        $pkendaraan = PKendaraan::findOrFail($id);
        $drivers = MDriver::all(); // Ambil semua data driver dari database

        // Konversi format tanggal ke YYYY-MM-DD sebelum disimpan
        $pkendaraan->tgl_berangkat = Carbon::createFromFormat('d-m-Y', $request->tgl_berangkat1)->format('Y-m-d');
        $pkendaraan->divisi = $request->divisi1;
        $pkendaraan->nama_pic = $request->nama_pic1;
        $pkendaraan->jam_berangkat = $request->jam_berangkat1;
        $pkendaraan->jam_kembali = $request->jam_kembali1;
        $pkendaraan->jenis_tujuan = $request->jenis_tujuan1;
        $pkendaraan->tujuan = $request->tujuan1;
        $pkendaraan->pejemputan = $request->pejemputan1;
        $pkendaraan->no_wa = $request->no_wa1;
        $pkendaraan->no_polisi = $request->no_polisi1;
        $pkendaraan->ket = $request->ket1;

        $pkendaraan->driver = ($request->driver1 === 'Rental') ? null : $request->driver1;
        // $pkendaraan->rental_driver = $request->rental_driver;
        // $pkendaraan->rental_kendaraan = $request->rental_kendaraan;

        // Jika driver adalah null, simpan data rental
        if ($pkendaraan->driver === null) {
            $pkendaraan->rental_driver = $request->rental_driver;
            $pkendaraan->rental_kendaraan = $request->rental_kendaraan;
        } else if ($request->driver === 99){
            $pkendaraan->driver = 99;
        } else {
            // Jika ada driver, reset rental_driver dan rental_kendaraan ke null
            $pkendaraan->rental_driver = null;
            $pkendaraan->rental_kendaraan = null;
        }


        if ($request->hasFile('file_memo1')) {
            $request->validate([
                'file_memo1' => 'mimes:pdf,jpg,jpeg,png|max:2048', // Maksimal 2MB
            ]);

            // Hapus file lama jika ada
            if ($pkendaraan->file_memo && file_exists(public_path('uploads/memo/' . basename($pkendaraan->file_memo)))) {
                unlink(public_path('uploads/memo/' . basename($pkendaraan->file_memo)));
            }

            // Simpan file baru
            $file = $request->file('file_memo1');
            $name = $file->getClientOriginalName();
            $namefile = time() . '_' . $name;
            $file->move(public_path('uploads/memo'), $namefile);

            // Simpan path relatif
            $pkendaraan->file_memo = 'uploads/memo/' . $namefile;
        }

        $pkendaraan->save();

        session()->flash('success', 'Data berhasil diperbarui.');
        return response()->json([
            'redirect_url' => route('pkendaraan.index'),
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $pkendaraan = PKendaraan::findOrFail($id);

        // Update status menjadi 0 (dibatalkan)
        $pkendaraan->status = 0;
        $pkendaraan->apprv = Auth::user()->master_user_nama;
        $pkendaraan->save();

        return redirect()->route('pkendaraan.index')->with('success', 'Permintaan Kendaraan dibatalkan.');
    }

    public function export(Request $request)
    {
        // dd('test');
        // Get the filter parameters from the request
        $tgl_awal = $request->input('tgl_awal');
        $jenis_tujuan = $request->input('jenis_tujuan');
        $divisi = $request->input('id_divisi');
        $status = $request->input('status');

        // dd($id_group);

        // Query the data based on filters
        // $pkendaraan = PKendaraan::query();
        $pkendaraan = PKendaraan::with(['driverDetail', 'kendaraanDetail']);

        // $kaskecil = Kaskecil::query();

        if ($tgl_awal) {
            $pkendaraan->where('tgl_berangkat', '>=', $tgl_awal);
        }

        if ($jenis_tujuan) {
            $pkendaraan->where('jenis_tujuan', '=', $jenis_tujuan);
        }

        if ($divisi) {
            if ($divisi <> 'all') {
                $pkendaraan->where('divisi', '=', $divisi);
            }
        }

        if ($status) {
            if ($status <> 'all') {
                $pkendaraan->where('status', '=', $status);
            }
        }


        // Fetch the data
        $data = $pkendaraan->get();
        // dd($data);
        // dd($data->first()->group, $data->first()->gl, $data->first()->cc);


        // Export logic (using Laravel Excel)
        return Excel::download(new PKendaraanExport($data), 'Permintaan_Kendaraan_export.xlsx');
    }

//     public function multiApprove(Request $request)
// {
//     // Pastikan user adalah admin yang berhak (opsional, tergantung logic Auth::user()->master_user_nama)
//     if (!in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga'])) {
//         return response()->json(['message' => 'Akses ditolak.'], 403);
//     }

//     $ids = $request->input('ids');
//     if (!is_array($ids) || empty($ids)) {
//         return response()->json(['message' => 'Tidak ada data yang dipilih.'], 400);
//     }

//     $approvedCount = 0;
//     $rejectedCount = 0;
//     $failedApproves = [];

//     // Lakukan loop untuk setiap ID yang dipilih
//     foreach ($ids as $id) {
//         $pkendaraan = PKendaraan::find($id);

//         // Pastikan data ada dan status masih 'Pengajuan Divisi' (1)
//         if (!$pkendaraan || $pkendaraan->status != 1) {
//             $rejectedCount++; // Atau bisa diabaikan
//             continue;
//         }

//         $tgl_berangkat = $pkendaraan->tgl_berangkat;
//         $jam_berangkat = $pkendaraan->jam_berangkat;
//         $jam_kembali = $pkendaraan->jam_kembali;
//         $driver = $pkendaraan->driver;
//         $no_polisi = $pkendaraan->no_polisi;

//         // Cek bentrok driver
//         $driver_bentrok = false;
//         if (!is_null($driver)) {
//             $driver_bentrok = PKendaraan::where('id', '!=', $id) // Kecuali data ini sendiri (walaupun status belum approved, tapi untuk jaga-jaga)
//                 ->where('tgl_berangkat', $tgl_berangkat)
//                 ->where('status', 2) // Hanya yang sudah approved
//                 ->where('driver', $driver)
//                 ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
//                     $query->where(function ($q) use ($jam_berangkat, $jam_kembali) {
//                         $q->where('jam_berangkat', '<', $jam_kembali)
//                             ->where('jam_kembali', '>', $jam_berangkat);
//                     });
//                 })
//                 ->exists();
//         }

//         // Cek bentrok kendaraan
//         $kendaraan_bentrok = false;
//         if (!is_null($no_polisi)) {
//             $kendaraan_bentrok = PKendaraan::where('id', '!=', $id) // Kecuali data ini sendiri
//                 ->where('tgl_berangkat', $tgl_berangkat)
//                 ->where('status', 2) // Hanya yang sudah approved
//                 ->where('no_polisi', $no_polisi)
//                 ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
//                     $query->where(function ($q) use ($jam_berangkat, $jam_kembali) {
//                         $q->where('jam_berangkat', '<', $jam_kembali)
//                             ->where('jam_kembali', '>', $jam_berangkat);
//                     });
//                 })
//                 ->exists();
//         }

//         if ($driver_bentrok || $kendaraan_bentrok) {
//             $rejectedCount++;
//             $failedApproves[] = "ID {$id} (PIC: {$pkendaraan->nama_pic}) - Driver atau Kendaraan bentrok.";
//             continue; // Lanjut ke pengajuan berikutnya
//         }

//         // Jika tidak bentrok, setujui permintaan
//         $pkendaraan->status = 2; // Approved
//         $pkendaraan->apprv = Auth::user()->master_user_nama;
//         $pkendaraan->save();
//         $approvedCount++;
//     }

//     $message = "Berhasil menyetujui **{$approvedCount}** data.";
//     if ($rejectedCount > 0) {
//         $message .= " Gagal menyetujui **{$rejectedCount}** data karena sudah tidak dalam status pengajuan atau bentrok dengan jadwal lain.";
//         // Anda bisa menambahkan detail failedApproves di sini jika diperlukan.
//     }

//     return response()->json(['message' => $message]);
// }


//    public function approve($id)
// {
//     // 1. Cari data pengajuan kendaraan berdasarkan ID
//     $pkendaraan = PKendaraan::find($id);

//     if (!$pkendaraan) {
//         // Jika data tidak ditemukan, redirect dengan pesan error
//         return redirect()->route('pkendaraan.index')->with('error', 'Data pengajuan tidak ditemukan.');
//     }

//     // Pastikan hanya pengajuan yang masih berstatus 1 (Pengajuan Divisi) yang bisa di-approve
//     if ($pkendaraan->status != 1) {
//          return redirect()->route('pkendaraan.index')->with('error', 'Pengajuan ini sudah tidak dalam status menunggu persetujuan.');
//     }

//     // Ambil detail waktu dan sumber daya dari pengajuan yang akan di-approve
//     $tgl_berangkat = $pkendaraan->tgl_berangkat;
//     $jam_berangkat = $pkendaraan->jam_berangkat;
//     $jam_kembali = $pkendaraan->jam_kembali;
//     $driver = $pkendaraan->driver;
//     $no_polisi = $pkendaraan->no_polisi;

//     // Inisialisasi status bentrok
//     $driver_bentrok = false;
//     $kendaraan_bentrok = false;

//     // 2. Cek apakah driver sudah digunakan dalam rentang waktu yang sama (jika driver ada)
//     if (!is_null($driver) && $driver != 99) { // Kecuali jika Driver Online (ID 99) atau Rental (null)
//         $driver_bentrok = PKendaraan::where('tgl_berangkat', $tgl_berangkat)
//             ->where('status', 2) // Hanya yang sudah Approved
//             ->where('driver', $driver)
//             ->where('id', '!=', $id) // Kecualikan pengajuan ini sendiri (walau status masih 1)
//             ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
//                 // Cek tumpang tindih waktu: (StartA < EndB) AND (EndA > StartB)
//                 $query->where('jam_berangkat', '<', $jam_kembali)
//                     ->where('jam_kembali', '>', $jam_berangkat);
//             })
//             ->exists();
//     }

//     // 3. Cek apakah kendaraan sudah digunakan dalam rentang waktu yang sama (jika kendaraan ada)
//     if (!is_null($no_polisi)) {
//         $kendaraan_bentrok = PKendaraan::where('tgl_berangkat', $tgl_berangkat)
//             ->where('status', 2) // Hanya yang sudah Approved
//             ->where('no_polisi', $no_polisi)
//             ->where('id', '!=', $id) // Kecualikan pengajuan ini sendiri
//             ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
//                 // Cek tumpang tindih waktu: (StartA < EndB) AND (EndA > StartB)
//                 $query->where('jam_berangkat', '<', $jam_kembali)
//                     ->where('jam_kembali', '>', $jam_berangkat);
//             })
//             ->exists();
//     }

//     // 4. Proses persetujuan atau tolak karena bentrok
//     if ($driver_bentrok || $kendaraan_bentrok) {
//         $errorMessage = 'Persetujuan gagal: ';
//         if ($driver_bentrok) {
//             $errorMessage .= 'Driver sudah digunakan pada waktu yang sama. ';
//         }
//         if ($kendaraan_bentrok) {
//             $errorMessage .= 'Kendaraan sudah digunakan pada waktu yang sama.';
//         }
        
//         return redirect()->route('pkendaraan.index')->with('error', $errorMessage);
//     }

//     // 5. Jika tidak bentrok, setujui permintaan
//     $pkendaraan->status = 2; // Approved
//     $pkendaraan->apprv = Auth::user()->master_user_nama; // Catat siapa yang menyetujui
//     $pkendaraan->save();

//     return redirect()->route('pkendaraan.index')->with('success', 'Data berhasil disetujui.');
// }

private function checkAssignment(PKendaraan $pkendaraan)
    {
        // 1. Kasus Driver Online (ID 99)
        if ($pkendaraan->driver == 99) {
            return true;
        }
        
        // 2. Kasus Penugasan Standar
        $hasStandardAssignment = !is_null($pkendaraan->driver) && !is_null($pkendaraan->no_polisi);

        // 3. Kasus Rental
        $isRental = is_null($pkendaraan->driver) && is_null($pkendaraan->no_polisi);
        $hasCompleteRental = $isRental && !is_null($pkendaraan->rental_driver) && !is_null($pkendaraan->rental_kendaraan);

        // Pengajuan valid jika: Punya penugasan standar ATAU Punya penugasan rental lengkap.
        if ($hasStandardAssignment || $hasCompleteRental) {
            return true;
        }

        return false;
    }

    // ----------------------------------------------------
    // ⭐ PUBLIC FUNCTION APPROVE ($id)
    // ----------------------------------------------------
    public function approve($id)
    {
        // 1. Cari data pengajuan kendaraan berdasarkan ID
        $pkendaraan = PKendaraan::find($id);

        if (!$pkendaraan) {
            return redirect()->route('pkendaraan.index')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        // Pastikan hanya pengajuan yang masih berstatus 1 (Pengajuan Divisi) yang bisa di-approve
        if ($pkendaraan->status != 1) {
            return redirect()->route('pkendaraan.index')->with('error', 'Pengajuan ini sudah tidak dalam status menunggu persetujuan.');
        }

        // 2. VALIDASI WAJIB PENUGASAN
        if (!$this->checkAssignment($pkendaraan)) {
            return redirect()->route('pkendaraan.index')->with('error', 'Persetujuan gagal: Mohon lengkapi penugasan Driver/Kendaraan sebelum menyetujui.');
        }


        // 3. Ambil detail waktu dan sumber daya
        $tgl_berangkat = $pkendaraan->tgl_berangkat;
        $jam_berangkat = $pkendaraan->jam_berangkat;
        $jam_kembali = $pkendaraan->jam_kembali;
        $driver = $pkendaraan->driver;
        $no_polisi = $pkendaraan->no_polisi;

        $driver_bentrok = false;
        $kendaraan_bentrok = false;

        // 4. Cek Bentrok Driver (Hanya untuk driver standar)
        if (!is_null($driver) && $driver != 99) { 
            $driver_bentrok = PKendaraan::where('tgl_berangkat', $tgl_berangkat)
                ->where('status', 2) // Hanya yang sudah Approved
                ->where('driver', $driver)
                ->where('id', '!=', $id) // Kecualikan pengajuan ini sendiri
                ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
                    $query->where('jam_berangkat', '<', $jam_kembali)
                        ->where('jam_kembali', '>', $jam_berangkat);
                })
                ->exists();
        }

        // 5. Cek Bentrok Kendaraan (Hanya untuk kendaraan standar)
        if (!is_null($no_polisi)) {
            $kendaraan_bentrok = PKendaraan::where('tgl_berangkat', $tgl_berangkat)
                ->where('status', 2) // Hanya yang sudah Approved
                ->where('no_polisi', $no_polisi)
                ->where('id', '!=', $id) // Kecualikan pengajuan ini sendiri
                ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
                    $query->where('jam_berangkat', '<', $jam_kembali)
                        ->where('jam_kembali', '>', $jam_berangkat);
                })
                ->exists();
        }

        // 6. Proses persetujuan atau tolak karena bentrok
        if ($driver_bentrok || $kendaraan_bentrok) {
            $errorMessage = 'Persetujuan gagal: ';
            if ($driver_bentrok) {
                $errorMessage .= 'Driver sudah digunakan pada waktu yang sama. ';
            }
            if ($kendaraan_bentrok) {
                $errorMessage .= 'Kendaraan sudah digunakan pada waktu yang sama.';
            }
            
            return redirect()->route('pkendaraan.index')->with('error', $errorMessage);
        }

        // 7. Jika tidak bentrok, setujui permintaan
        $pkendaraan->status = 2; // Approved
        $pkendaraan->apprv = Auth::user()->master_user_nama; // Catat siapa yang menyetujui
        $pkendaraan->save();

        return redirect()->route('pkendaraan.index')->with('success', 'Data berhasil disetujui.');
    }


    // ----------------------------------------------------
    // ⭐ PUBLIC FUNCTION MULTI APPROVE (Request $request)
    // ----------------------------------------------------
    public function multiApprove(Request $request)
    {
        // 1. Autentikasi dan Validasi Request
        if (!in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $ids = $request->input('ids');
        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Tidak ada data yang dipilih.'], 400);
        }

        $approvedCount = 0;
        $rejectedCount = 0;
        $failedApproves = [];

        // 2. Proses Persetujuan Massal
        foreach ($ids as $id) {
            $pkendaraan = PKendaraan::find($id);

            // Cek Status dan Keberadaan Data
            if (!$pkendaraan || $pkendaraan->status != 1) {
                $rejectedCount++;
                $failedApproves[] = "ID {$id} (Status tidak valid).";
                continue; 
            }

            // VALIDASI WAJIB PENUGASAN
            if (!$this->checkAssignment($pkendaraan)) {
                $rejectedCount++;
                $failedApproves[] = "ID {$id} (PIC: {$pkendaraan->nama_pic}) - Driver/Kendaraan belum ditetapkan.";
                continue; 
            }

            $tgl_berangkat = $pkendaraan->tgl_berangkat;
            $jam_berangkat = $pkendaraan->jam_berangkat;
            $jam_kembali = $pkendaraan->jam_kembali;
            $driver = $pkendaraan->driver;
            $no_polisi = $pkendaraan->no_polisi;
            
            $driver_bentrok = false;
            $kendaraan_bentrok = false;

            // Cek bentrok driver (hanya jika driver standar)
            if (!is_null($driver) && $driver != 99) {
                $driver_bentrok = PKendaraan::where('id', '!=', $id) 
                    ->where('tgl_berangkat', $tgl_berangkat)
                    ->where('status', 2)
                    ->where('driver', $driver)
                    ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
                        $query->where('jam_berangkat', '<', $jam_kembali)
                            ->where('jam_kembali', '>', $jam_berangkat);
                    })
                    ->exists();
            }

            // Cek bentrok kendaraan (hanya jika kendaraan standar)
            if (!is_null($no_polisi)) {
                $kendaraan_bentrok = PKendaraan::where('id', '!=', $id)
                    ->where('tgl_berangkat', $tgl_berangkat)
                    ->where('status', 2)
                    ->where('no_polisi', $no_polisi)
                    ->where(function ($query) use ($jam_berangkat, $jam_kembali) {
                        $query->where('jam_berangkat', '<', $jam_kembali)
                            ->where('jam_kembali', '>', $jam_berangkat);
                    })
                    ->exists();
            }

            if ($driver_bentrok || $kendaraan_bentrok) {
                $rejectedCount++;
                $failedApproves[] = "ID {$id} (PIC: {$pkendaraan->nama_pic}) - Driver atau Kendaraan bentrok.";
                continue;
            }

            // Jika lulus semua cek, setujui permintaan
            $pkendaraan->status = 2; // Approved
            $pkendaraan->apprv = Auth::user()->master_user_nama;
            $pkendaraan->save();
            $approvedCount++;
        }

        // 3. Respon Akhir
        $message = "Berhasil menyetujui **{$approvedCount}** data.";
        if ($rejectedCount > 0) {
            $message .= " Gagal menyetujui **{$rejectedCount}** data. Driver/Kendaraan belum ditetapkan";
        }

        return response()->json(['message' => $message]);
    }

    public function reject($id)
    {
        $pkendaraan = PKendaraan::find($id);
        $pkendaraan->status = 3; // Rejected
        $pkendaraan->apprv = Auth::user()->master_user_nama;
        $pkendaraan->save();

        return redirect()->route('pkendaraan.index')->with('error', 'Data rejected.');
    }
}
