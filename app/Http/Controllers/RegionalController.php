<?php

namespace App\Http\Controllers;

use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RegionalController extends Controller
{
    public function index()
    {
        $regional = Regional::all();
        return view('admin.regional.regional', compact('regional'));
    }

    public function store(Request $request)
    {
        Regional::create([
            'nama_regional' => $request->nama_regional,
        ]);
        Session::flash('success', "Berhasil disimpan");
        return redirect()->back()->with('success', 'Data berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $regional = Regional::find($id);
        $regional->update([
            'nama_regional' => $request->nama_regional,
        ]);
        Session::flash('success', "Berhasil diupdate");
       return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }
}
