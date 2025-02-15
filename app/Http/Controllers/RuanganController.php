<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangan = Ruangan::all();
        return view('admin.ruangan.ruangan', compact('ruangan'));
    }

    public function store(Request $request)
    {
        Ruangan::create([
            'nama' => $request->nama,
            'lantai' => $request->lantai,
            'kapasitas' => $request->kapasitas,
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
            'status' => $request->status
        ]);

        return redirect()->route('admin.ruangan.index')
            ->with('success', 'Berhasil diupdate');
    }
}
