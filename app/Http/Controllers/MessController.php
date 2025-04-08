<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mess;
use App\Models\MessModel;
use App\Models\MessPhoto;

use Illuminate\Support\Facades\Storage;

class MessController extends Controller
{
    public function index()
    {
        $messes = MessModel::with('photos')->get();
        return view('mess.index', compact('messes'));
    }


    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama' => 'required',
            'lokasi' => 'required',
            'cp' => 'required',
            'no_cp' => 'required',
            'deskripsi' => 'nullable',
            'foto_utama' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_pendukung.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
        // dd('foto');

        // $mess = MessModel::create($request->only('nama', 'lokasi', 'deskripsi'));
        $data = $request->only('nama', 'lokasi', 'deskripsi');
        $data['status'] = 1; // Tambahkan status = 1

        $mess = MessModel::create($data);
        
        // Simpan Foto Utama
        if ($request->hasFile('foto_utama')) {
            $path = $request->file('foto_utama')->store('uploads/mess', 'public');
            MessPhoto::create([
                'mess_id' => $mess->id,
                'foto' => $path,
                'is_utama' => true  // Tandai sebagai foto utama
            ]);
        }

        // Simpan Foto Pendukung (jika ada)
        if ($request->hasFile('foto_pendukung')) {
            foreach ($request->file('foto_pendukung') as $file) {
                $path = $file->store('uploads/mess', 'public');
                MessPhoto::create([
                    'mess_id' => $mess->id,
                    'foto' => $path,
                    'is_utama' => false  // Foto pendukung
                ]);
            }
        }

        return redirect()->route('mess.index')->with('success', 'Mess berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $mess = MessModel::with('photos')->findOrFail($id);
        return response()->json($mess);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string',
            'cp' => 'required|string',
            'no_cp' => 'required|string',
            'deskripsi' => 'nullable|string',
            'foto_utama' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_pendukung.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $mess = MessModel::findOrFail($id);
        $mess->update([
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'cp' => $request->cp,
            'no_cp' => $request->no_cp
        ]);

        // Update Foto Utama jika ada file baru
        if ($request->hasFile('foto_utama')) {
            // Hapus foto utama lama dari storage & database
            $fotoUtamaLama = MessPhoto::where('mess_id', $mess->id)->where('is_utama', true)->first();
            if ($fotoUtamaLama) {
                Storage::delete('public/' . $fotoUtamaLama->foto);
                $fotoUtamaLama->delete();
            }

            // Simpan foto utama baru
            $fotoUtamaPath = $request->file('foto_utama')->store('uploads/mess', 'public');
            MessPhoto::create([
                'mess_id' => $mess->id,
                'foto' => $fotoUtamaPath,
                'is_utama' => true
            ]);
        }

        // Tambah Foto Pendukung jika ada
        if ($request->hasFile('foto_pendukung')) {
            foreach ($request->file('foto_pendukung') as $file) {
                $path = $file->store('uploads/mess', 'public');
                MessPhoto::create([
                    'mess_id' => $mess->id,
                    'foto' => $path,
                    'is_utama' => false // Foto pendukung
                ]);
            }
        }

        return redirect()->route('mess.index')->with('success', 'Data Mess berhasil diperbarui');
    }

    public function destroy($id)
    {
        $mess = MessModel::findOrFail($id);

        // Hapus semua foto yang terkait
        // foreach ($mess->photos as $photo) {
        //     Storage::delete('public/' . $photo->foto);
        //     $photo->delete();
        // }

        // Hapus mess
        // $mess->delete();
        $mess->update([
            'status' => 0,
        ]);

        return redirect()->route('mess.index')->with('success', 'Mess berhasil nonaktifkan.');
    }

    public function aktif($id)
    {
        $mess = MessModel::findOrFail($id);

        
        $mess->update([
            'status' => 1,
        ]);

        return redirect()->route('mess.index')->with('success', 'Mess berhasil diaktifkan.');
    }



}
