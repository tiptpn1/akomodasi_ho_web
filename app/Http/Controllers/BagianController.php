<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bagian;
use Illuminate\Support\Facades\Session;

class BagianController extends Controller
{
    public function index()
    {
        $bagian = Bagian::all();
        return view('admin.bagian.bagian', compact('bagian'));
    }
    public function store(Request $request)
    {
        Bagian::create([
            'master_bagian_nama' => $request->master_bagian_nama,
            'master_bagian_posisi' => $request->master_bagian_posisi,
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
            'is_active' => $request->is_active,
            'master_bagian_kode' => $request->master_bagian_kode,
        ]);
        Session::flash('success', "Berhasil diupdate");
        return redirect()->route('admin.bagian.index');
    }
}
