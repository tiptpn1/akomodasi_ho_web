<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use App\Models\MRegional;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangan = Ruangan::with('regional')->get();
        $regionals = MRegional::all(); // Ini yang baru ditambahkan
        return view('admin.ruangan.ruangan', compact('ruangan', 'regionals'));
    }

    public function store(Request $request)
    {
        Ruangan::create([
            'nama' => $request->nama,
            'lantai' => $request->lantai,
            'kapasitas' => $request->kapasitas,
            'ruangan_regional_id' => $request->ruangan_regional_id,
            'status' => $request->status
        ]);
        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::find($id);
        $ruangan->update([
            'nama' => $request->nama,
            'lantai' => $request->lantai,
            'kapasitas' => $request->kapasitas,
            'ruangan_regional_id' => $request->ruangan_regional_id,
            'status' => $request->status
        ]);

        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Berhasil diupdate');
    }
}
