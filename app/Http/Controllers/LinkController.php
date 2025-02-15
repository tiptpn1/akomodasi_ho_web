<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterLink;

class LinkController extends Controller
{
    public function index()
    {
        $masterlink = MasterLink::all();
        return view('admin.link.link', compact('masterlink'));
    }
    public function store(Request $request)
    {
        MasterLink::create([
            'namalink' => $request->namalink,
            'link' => $request->link,
            'status' => $request->status
        ]);
        return redirect()->route('admin.masterlink.index')->with('success', 'Berhasil disimpan');
    }

    public function update(Request $request, $id)
    {

        $masterlink = MasterLink::find($id);
        $masterlink->update([
            'namalink' => $request->namalink,
            'link' => $request->link,
            'status' =>  $request->status
        ]);

        return redirect()->route('admin.masterlink.index')->with('success', 'Berhasil diupdate');
    }
}
