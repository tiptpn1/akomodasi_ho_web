<?php

namespace App\Http\Controllers;

use App\Exports\MasterRegionalExport;
use Illuminate\Http\Request;
use App\Models\MRegional;
use Maatwebsite\Excel\Facades\Excel;

class MRegionalController extends Controller
{
    public function index()
    {
        $regionals = MRegional::all(); // Mengambil semua data dari tabel regional
        return view('regional.index', compact('regionals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_regional' => 'required|string|max:100',
        ]);

        MRegional::create([
            'nama_regional' => $request->nama_regional,
        ]);

        return redirect('/masterregional')->with('success', 'Regional berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = MRegional::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {

        // Find the KasKecil record by ID
        $masterregional = MRegional::findOrFail($id);


        $masterregional->nama_regional = $request->nama_regional1;

        // Save the updated record
        $masterregional->save();

        session()->flash('success', 'Data berhasil diperbarui.');

        // Redirect back with a success message
        return response()->json([
            'redirect_url' => url('/masterregional'),
            'message' => 'Data berhasil diperbarui.'
        ]);
    }


    public function destroy($id)
    {
        try {
            $masterregional = MRegional::findOrFail($id);
            $masterregional->delete();
            return redirect()->to(url('/masterregional'))->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->to(url('/masterregional'))->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function export(Request $request)
    {
        $data = MRegional::all(); // Ambil semua data kendaraan
        return Excel::download(new MasterRegionalExport($data), 'masterregional_export.xlsx');
    }
}
