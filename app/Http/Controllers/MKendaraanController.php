<?php

namespace App\Http\Controllers;

use App\Exports\MasterKendaraanExport;
use Illuminate\Http\Request;
use App\Models\MKendaraan;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class MKendaraanController extends Controller
{
    public function index()
    {
        // $kendaraans = MKendaraan::all(); // Mengambil semua data dari tabel kendaraan
        $kendaraans = MKendaraan::where('kendaraan_regional_id',Auth::user()->bagian->regional->id_regional)->get();
        return view('kendaraan.index', compact('kendaraans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nopol' => 'required|string|max:20|unique:m_kendaraan,nopol',
            'tipe_kendaraan' => 'required|string|max:100',
            'kepemilikan' => 'required|string|max:100',
        ]);

        MKendaraan::create([
            'kendaraan_regional_id' => Auth::user()->bagian->regional->id_regional,
            'nopol' => $request->nopol,
            'tipe_kendaraan' => $request->tipe_kendaraan,
            'kepemilikan' => $request->kepemilikan,
        ]);

        return redirect('/masterkendaraan')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = MKendaraan::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {

        // Find the KasKecil record by ID
        $masterkendaraan = MKendaraan::findOrFail($id);


        $masterkendaraan->nopol = $request->nopol1;
        $masterkendaraan->tipe_kendaraan = $request->tipe_kendaraan1;
        $masterkendaraan->kepemilikan = $request->kepemilikan1;

        // Save the updated record
        $masterkendaraan->save();

        session()->flash('success', 'Data berhasil diperbarui.');

        // Redirect back with a success message
        return response()->json([
            'redirect_url' => url('/masterkendaraan'),
            'message' => 'Data berhasil diperbarui.'
        ]);
    }


    public function destroy($id)
    {
        try {
            $masterkendaraan = MKendaraan::findOrFail($id);
            $masterkendaraan->delete();
            return redirect()->to(url('/masterkendaraan'))->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->to(url('/masterkendaraan'))->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function export(Request $request)
    {
        // $data = MKendaraan::all(); // Ambil semua data kendaraan
        $data = MKendaraan::where('kendaraan_regional_id',Auth::user()->bagian->regional->id_regional)->get();
        return Excel::download(new MasterKendaraanExport($data), 'masterkendaraan_export.xlsx');
    }
}
