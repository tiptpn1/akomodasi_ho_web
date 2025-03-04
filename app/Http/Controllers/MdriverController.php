<?php

namespace App\Http\Controllers;

use App\Exports\MasterDriverExport;
use Illuminate\Http\Request;
use App\Models\MDriver;
use Maatwebsite\Excel\Facades\Excel;

class MdriverController extends Controller
{
    public function index()
    {
        $drivers = MDriver::all(); // Mengambil semua data dari tabel driver
        return view('driver.index', compact('drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_driver' => 'required|string|max:100',
        ]);

        MDriver::create([
            'nama_driver' => $request->nama_driver,
            'no_hp' => $request->no_hp,
        ]);

        return redirect('/masterdriver')->with('success', 'Driver berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = MDriver::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {

        // Find the KasKecil record by ID
        $masterdriver = MDriver::findOrFail($id);


        $masterdriver->nama_driver = $request->nama_driver1;
        $masterdriver->no_hp = $request->no_hp1;

        // Save the updated record
        $masterdriver->save();

        session()->flash('success', 'Data berhasil diperbarui.');

        // Redirect back with a success message
        return response()->json([
            'redirect_url' => url('/masterdriver'),
            'message' => 'Data berhasil diperbarui.'
        ]);
    }


    public function destroy($id)
    {
        try {
            $masterdriver = MDriver::findOrFail($id);
            $masterdriver->delete();
            return redirect()->to(url('/masterdriver'))->with('success', 'Data berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->to(url('/masterdriver'))->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function export(Request $request)
    {
        $data = MDriver::all(); // Ambil semua data driver
        return Excel::download(new MasterDriverExport($data), 'masterdriver_export.xlsx');
    }
}
