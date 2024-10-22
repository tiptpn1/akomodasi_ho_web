<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HakAkses;

class HakAksesController extends Controller
{
    public function index()
    {
        $hak_akses = HakAkses::all();
        return view('admin.hak_akses.hak_akses', compact('hak_akses'));
    }

    public function store(Request $request)
    {
        HakAkses::create([
            'hak_akses_nama' => $request->hak_akses_nama,
            'status' => $request->status
        ]);
        return redirect()->route('admin.hak_akses.index')
            ->with('success', 'Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {
        $hak_akses = HakAkses::find($id);
        $hak_akses->update([
            'hak_akses_nama' => $request->hak_akses_nama,
            'status' => $request->status
        ]);

        return redirect()->route('admin.hak_akses.index')
            ->with('success', 'Berhasil diupdate');
    }
}
