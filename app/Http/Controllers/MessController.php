<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mess;
use App\Models\MessModel;
use App\Models\MessPhoto;
use App\Models\PetugasMess;
use Illuminate\Support\Facades\File;
use App\Services\OpenRouteService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

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
        
        
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'lokasi' => 'required',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'cp' => 'required|array',
            'cp.*' => 'required|string',
            'no_cp' => 'required|array',
            'no_cp.*' => 'required|string',
            'deskripsi' => 'nullable',
            'foto_utama' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_pendukung.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput(); // agar input lama tetap muncul di form
        }
        // dd($request->all());
        $kantorLat = env('KANTOR_LAT');
        $kantorLng = env('KANTOR_LNG');
        $result = OpenRouteService::getDistanceAndDuration(
            $request->lat,
            $request->lng,
            $kantorLat,
            $kantorLng
        );
        // dd($result['duration_min']);
        // dd($request->all());
        // $mess = MessModel::create($request->only('nama', 'lokasi', 'deskripsi'));
        $data = $request->only('nama', 'lokasi', 'deskripsi');
        $data['status'] = 1; // Tambahkan status = 1
        $data['jarak']=$result['distance_km'];
        $data['waktu']=$result['duration_min'];
        $data['last_distance_sync']= Carbon::now();
        $data['lat']=$request->lat;
        $data['lng']=$request->lng;
        $mess = MessModel::create($data);
        // Simpan data petugas
        foreach ($request->cp as $index => $namaPetugas) {
            \App\Models\PetugasMess::create([
                'mess_id' => $mess->id,
                'nama_petugas' => $namaPetugas,
                'no_petugas' => $request->no_cp[$index]
            ]);
        }
        
        if ($request->hasFile('foto_utama')) {
            $file = $request->file('foto_utama');
            $filename = time() . '_' . $file->getClientOriginalName();
            $destinationPath = public_path('uploads/mess');
            
            // Pastikan folder tujuan ada
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }
        
            $file->move($destinationPath, $filename);
        
            MessPhoto::create([
                'mess_id' => $mess->id,
                'foto' => 'uploads/mess/' . $filename,
                'is_utama' => true  // Tandai sebagai foto utama
            ]);
        }
        
        // Simpan Foto Pendukung (jika ada)
        if ($request->hasFile('foto_pendukung')) {
            foreach ($request->file('foto_pendukung') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $destinationPath = public_path('uploads/mess');
                
                // Pastikan folder tujuan ada
                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }
        
                $file->move($destinationPath, $filename);
        
                MessPhoto::create([
                    'mess_id' => $mess->id,
                    'foto' => 'uploads/mess/' . $filename,
                    'is_utama' => false  // Foto pendukung
                ]);
            }
        }

        return redirect()->route('mess.index')->with('success', 'Mess berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $mess = MessModel::with(['photos', 'petugas'])->findOrFail($id);
        return response()->json($mess);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'lokasi' => 'required|string',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'cp' => 'required|array',
            'cp.*' => 'required|string',
            'no_cp' => 'required|array',
            'no_cp.*' => 'required|string',
            'deskripsi' => 'nullable|string',
            'foto_utama' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_pendukung.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput(); // agar input lama tetap muncul di form
        }
        $kantorLat = env('KANTOR_LAT');
        $kantorLng = env('KANTOR_LNG');
        $result = OpenRouteService::getDistanceAndDuration(
            $request->lat,
            $request->lng,
            $kantorLat,
            $kantorLng
        );
        $data['jarak']=$result['distance_km'];
        $data['waktu']=$result['duration_min'];
        $mess = MessModel::findOrFail($id);
        $mess->update([
            'nama' => $request->nama,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'jarak' => $result['distance_km'],
            'waktu' => $result['duration_min'],
            'last_distance_sync'=> Carbon::now(),
            'lat'=>$request->lat,
            'lng'=>$request->lng,
        ]);

        PetugasMess::where('mess_id', $mess->id)->delete();
        // Simpan ulang petugas baru
        foreach ($request->cp as $i => $nama) {
            PetugasMess::create([
                'mess_id' => $mess->id,
                'nama_petugas' => $nama,
                'no_petugas' => $request->no_cp[$i]
            ]);
        }


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

    public function updateJarak()
{
    $kantorLat = env('KANTOR_LAT');
    $kantorLng = env('KANTOR_LNG');

    $messList = MessModel::all();

    foreach ($messList as $mess) {
        $result = OpenRouteService::getDistanceAndDuration(
            $mess->lat,
            $mess->lng,
            $kantorLat,
            $kantorLng
        );

        if ($result) {
            $mess->jarak = $result['distance_km'];
            $mess->waktu = $result['duration_min'];
            $mess->last_distance_sync = now();
            $mess->save();
        }
    }

    return Redirect::back()->with('success', 'Estimasi jarak mess berhasil diperbarui.');
}



}
