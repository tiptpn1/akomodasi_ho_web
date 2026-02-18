<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bagian;
use App\Models\MRegional;
use Illuminate\Support\Facades\Session;

class BagianController extends Controller
{
    public function index()
    {
        $bagian =  Bagian::with('regional')->get();
        $regionals = MRegional::all(); // Ini yang baru ditambahkan
        return view('admin.bagian.bagian', compact('bagian', 'regionals'));
    }
    public function store(Request $request)
    {
        Bagian::create([
            'master_bagian_nama' => $request->master_bagian_nama,
            'master_bagian_posisi' => $request->master_bagian_posisi,
            'bagian_regional_id' => $request->bagian_regional_id,
            'is_active' => $request->is_active,
            'master_bagian_kode' => $request->master_bagian_kode
        ]);
        Session::flash('success', "Berhasil disimpan");
        return redirect()->route('admin.bagian.index');
    }

    public function update(Request $request, $id)
    {
        $bagian = Bagian::find($id);
        $bagian->update([
            'master_bagian_nama' => $request->master_bagian_nama,
            'master_bagian_posisi' => $request->master_bagian_posisi,
            'bagian_regional_id' => $request->bagian_regional_id,
            'is_active' => $request->is_active,
            'master_bagian_kode' => $request->master_bagian_kode,
        ]);
        Session::flash('success', "Berhasil diupdate");
        return redirect()->route('admin.bagian.index');
    }
}
