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

        if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga'])) {
            // Jika user adalah asisten_ga atau kasubdiv_ga, tampilkan semua data dengan join ke driver dan kendaraan
            $pkendaraan = PKendaraan::with(['driverDetail', 'kendaraanDetail'])->get();
        } else {
            // Jika bukan, tampilkan hanya data sesuai bagian user
            $pkendaraan = PKendaraan::with(['driverDetail', 'kendaraanDetail'])
                ->where('divisi', $divisi->master_bagian_nama)
                ->get();
        }

        $get_divisi = Bagian::get();

        $view_data = [
            'pkendaraan' => $pkendaraan,
            'divisi'     => $divisi->master_bagian_nama,
            'get_divisi' => $get_divisi,
        ];

        return view('pkendaraan.index', $view_data);
    }


    public function store(Request $request)
    {
        $request->validate([
            'file_memo' => 'required|mimes:pdf,jpg,jpeg,png|max:2048', // Maksimal 2MB
        ]);

        $pkendaraan = new PKendaraan();
        $pkendaraan->divisi = $request->divisi;
        $pkendaraan->nama_pic = $request->nama_pic;
        $pkendaraan->tgl_berangkat = Carbon::createFromFormat('d-m-Y', $request->tgl_berangkat)->format('Y-m-d');
        $pkendaraan->jenis_tujuan = $request->jenis_tujuan;
        $pkendaraan->jam_berangkat = $request->jam_berangkat;
        $pkendaraan->tujuan = $request->tujuan;
        $pkendaraan->pejemputan = $request->pejemputan;

        // Simpan file
        if ($request->hasFile('file_memo')) {
            $file = $request->file('file_memo');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/memo', $fileName);
            $pkendaraan->file_memo = 'memo/' . $fileName; // Simpan path
        }

        $pkendaraan->status = 1;
        $pkendaraan->save();

        return redirect()->route('pkendaraan.index')->with('success', 'Data berhasil disimpan!');
    }


    public function edit($id)
    {
        $data = PKendaraan::findOrFail($id);
        $drivers = MDriver::all(); // Ambil semua driver dari database
        $kendaraans = MKendaraan::all(); // Ambil semua kendaraan dari database

        return response()->json([
            'data' => $data,
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
        $pkendaraan->jenis_tujuan = $request->jenis_tujuan1;
        $pkendaraan->tujuan = $request->tujuan1;
        $pkendaraan->pejemputan = $request->pejemputan1;
        $pkendaraan->no_polisi = $request->no_polisi1;
        $pkendaraan->driver = $request->driver1;

        // **Proses Upload File (hanya jika ada unggahan)**
        if ($request->hasFile('file_memo1')) {
            $request->validate([
                'file_memo1' => 'mimes:pdf,jpg,jpeg,png|max:2048', // Maksimal 2MB
            ]);

            // Hapus file lama jika ada
            if ($pkendaraan->file_memo && Storage::exists('public/' . $pkendaraan->file_memo)) {
                Storage::delete('public/' . $pkendaraan->file_memo);
            }

            // Simpan file baru
            $file = $request->file('file_memo1');
            $path = $file->store('public/memo'); // Simpan ke storage
            $pkendaraan->file_memo = str_replace('public/', '', $path); // Simpan path relatif
        }

        $pkendaraan->save();

        return response()->json([
            'redirect_url' => route('pkendaraan.index'),
            'message' => 'Data berhasil diperbarui.'
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

    public function approve($id)
    {
        $pkendaraan = PKendaraan::find($id);
        $pkendaraan->status = 2; // Approved
        $pkendaraan->apprv = Auth::user()->master_user_nama;
        $pkendaraan->save();

        return redirect()->route('pkendaraan.index')->with('success', 'Data approved.');
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
