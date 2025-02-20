<?php

namespace App\Http\Controllers;

use App\Exports\MSExport;
use App\Models\Bagian;
use Illuminate\Http\Request;
use App\Models\SendVicon;
use App\Models\MakanSiang;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class MakanSiangController extends Controller
{
    public function index(Request $request)
    {
        $bagian=Auth::user()->master_nama_bagian_id;
        $divisi = Bagian::find($bagian);
        if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga'])) {
            // Jika user adalah asisten_ga atau kasubdiv_ga, tampilkan semua data
            $makansiang = MakanSiang::get();
        } else {
            // Jika bukan, tampilkan hanya data sesuai bagian user
            $makansiang = MakanSiang::where('divisi', $divisi->master_bagian_nama)->get();
        }
        $get_divisi=Bagian::get();

        $view_data = [
            'makansiang' => $makansiang,
            'divisi'     =>$divisi->master_bagian_nama,
            'get_divisi'    =>$get_divisi,
        ];
        return view('makansiang.index', $view_data);
        // return view('konsumsi.index', compact('konsumsi', 'start', 'bagians', 'request_status'));
    }

    public function store(Request $request)
    {
        // Create a new instance of your model (assuming your model is named `Kaskecil`)

        
        $tgl_pengajuan_split = explode('-', $request->tgl_pengajuan);
        $tgl_pengajuan = $tgl_pengajuan_split[2] . '-' . $tgl_pengajuan_split[1] . '-' . $tgl_pengajuan_split[0];

        $cekData = MakanSiang::where('divisi', $request->divisi)
        ->where('tgl_permintaan', $tgl_pengajuan)
        ->where('status', '!=', 0)
        ->exists();

        if ($cekData) {
            return redirect()->route('makansiang.index')->with('error', 'Data pengajuan divisi dengan tanggal tersebut sudah ada!');
        }

        $makansiang = new MakanSiang();

        // Fill the model with the data from the request
        // $makansiang->tgl_permintaan = $request->tgl_pengajuan;
        $makansiang->tgl_permintaan = $tgl_pengajuan;
        $makansiang->nama_pic = $request->nama_pengaju;
        $makansiang->divisi = $request->divisi;
        $makansiang->kadiv = $request->kadiv;
        $makansiang->jlh_karyawan = $request->jlh_karyawan;
        $makansiang->jlh_makan = $request->jlh_makansiang;
        $makansiang->username = Auth::user()->master_user_nama;
        $makansiang->status = 1;

        // Save the record
        $makansiang->save();

        // Redirect back or to a specific route with a success message
        return redirect()->route('makansiang.index')->with('success', 'Data berhasil disimpan!');
    }

    public function edit($id)
    {
        $data = MakanSiang::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {

        // Find the KasKecil record by ID
        $makansiang = MakanSiang::findOrFail($id);

        // Update the model attributes with the new data
        
        $tgl_pengajuan_split = explode('-', $request->tgl_pengajuan1);
        $tgl_pengajuan1 = $tgl_pengajuan_split[2] . '-' . $tgl_pengajuan_split[1] . '-' . $tgl_pengajuan_split[0];

        $makansiang->tgl_permintaan = $tgl_pengajuan1;
        $makansiang->nama_pic = $request->nama_pengaju1;
        $makansiang->divisi = $request->divisi1;
        $makansiang->kadiv = $request->kadiv1;
        $makansiang->jlh_karyawan = $request->jlh_karyawan1;
        $makansiang->jlh_makan = $request->jlh_makansiang1;
        $makansiang->username = Auth::user()->master_user_nama;
        $makansiang->apprv = Auth::user()->master_user_nama;


        // Save the updated record
        $makansiang->save();

        session()->flash('success', 'Data berhasil diperbarui.');

        // Redirect back with a success message
        return response()->json([
            'redirect_url' => route('makansiang.index'),
            'message' => 'Data berhasil diperbarui.'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $makansiang = MakanSiang::findOrFail($id);

        // Update status menjadi 0 (dibatalkan)
        $makansiang->status = 0;
        $makansiang->apprv = Auth::user()->master_user_nama;
        $makansiang->save();

        return redirect()->route('makansiang.index')->with('success', 'Permintaan makan siang dibatalkan.');
    }

    public function export(Request $request)
    {
        // dd('test');
        // Get the filter parameters from the request
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');
        $divisi = $request->input('id_divisi');
        $status = $request->input('status');

        // dd($id_group);

        // Query the data based on filters
        $makansiang = MakanSiang::query();

        // $kaskecil = Kaskecil::query();

        if ($tgl_awal) {
            $makansiang->where('tgl_permintaan', '>=', $tgl_awal);
        }

        if ($tgl_akhir) {
            $makansiang->where('tgl_permintaan', '<=', $tgl_akhir);
        }

        if ($divisi) {
            if($divisi<>'all')
            {
                $makansiang->where('divisi', '=', $divisi);
            }
            
           
        }

        if ($status) {
            if($status<>'all')
            {
                $makansiang->where('status', '=', $status);
            }
        }


        // Fetch the data
        $data = $makansiang->get();
        // dd($data);
        // dd($data->first()->group, $data->first()->gl, $data->first()->cc);


        // Export logic (using Laravel Excel)
        return Excel::download(new MSExport($data), 'makansiang_export.xlsx');
    }

    public function approve($id)
    {
        $makansiang = MakanSiang::find($id);
        $makansiang->status = 2; // Approved
        $makansiang->apprv = Auth::user()->master_user_nama;
        $makansiang->save();

        return redirect()->route('makansiang.index')->with('success', 'Data approved.');
    }

    public function reject($id)
    {
        $makansiang = MakanSiang::find($id);
        $makansiang->status = 3; // Rejected
        $makansiang->apprv = Auth::user()->master_user_nama;
        $makansiang->save();

        return redirect()->route('makansiang.index')->with('error', 'Data rejected.');
    }


    
}
