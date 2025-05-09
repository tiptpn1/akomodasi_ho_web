<?php
namespace App\Http\Controllers;

use App\Models\KamarModel;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Mess;
use App\Models\MessModel;
use App\Models\Jabatan;
use App\Models\KamarPhoto;
use App\Models\ReviewModel;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Validator;

class KamarController extends Controller
{
    public function index()
    {
        // $rooms = KamarModel::with(['mess', 'photos'])->get();
        // // dd($rooms);
        // $mess=MessModel::all();
        // $jabatan=Jabatan::all();
        // return view('kamar.index', compact('rooms','mess','jabatan'));
        $rooms = KamarModel::where('status', 1)->with(['mess', 'photos'])
        ->withAvg('reviews', 'rating') // Ambil rata-rata rating
        ->get();

        $mess = MessModel::where('status', 1)->get();
        $jabatan = Jabatan::all();
        foreach ($rooms as $room) {
            $peruntukanIds = explode(',', $room->peruntukan);
            $namaPeruntukan = $jabatan->whereIn('id', $peruntukanIds)->pluck('jabatan')->toArray();
            $room->peruntukan_teks = implode(', ', $namaPeruntukan);
        }

        return view('kamar.index', compact('rooms', 'mess', 'jabatan'));
    }

    public function create()
    {
        $messes = MessModel::all();
        return view('kamar.create', compact('messes'));
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'mess_id' => 'required',
            'nama_kamar' => 'required',
            'kapasitas' => 'required',
            'peruntukan' => 'required',
            'fasilitas' => 'nullable',
            'foto_utama' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'foto_pendukung.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput(); // agar input lama tetap muncul di form
        }

        $data = $request->only('mess_id', 'nama_kamar', 'kapasitas','peruntukan','fasilitas');
        $data['peruntukan'] = implode(',', $request->peruntukan);
        $data['status'] = 1; // Tambahkan status = 1

        $kamar = KamarModel::create($data);
        
        // Simpan Foto Utama
        // if ($request->hasFile('foto_utama')) {
        //     $path = $request->file('foto_utama')->store('uploads/kamar', 'public');
        //     KamarPhoto::create([
        //         'kamar_id' => $kamar->id,
        //         'foto' => $path,
        //         'is_utama' => true  // Tandai sebagai foto utama
        //     ]);
        // }
        if ($request->hasFile('foto_utama')) {
            $file = $request->file('foto_utama');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kamar'), $filename);
        
            KamarPhoto::create([
                'kamar_id' => $kamar->id,
                'foto' => 'uploads/kamar/' . $filename,
                'is_utama' => true
            ]);
        }

        // Simpan Foto Pendukung (jika ada)
        // if ($request->hasFile('foto_pendukung')) {
        //     foreach ($request->file('foto_pendukung') as $file) {
        //         $path = $file->store('uploads/kamar', 'public');
        //         KamarPhoto::create([
        //             'kamar_id' => $kamar->id,
        //             'foto' => $path,
        //             'is_utama' => false  // Foto pendukung
        //         ]);
        //     }
        // }
        if ($request->hasFile('foto_pendukung')) {
            foreach ($request->file('foto_pendukung') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/kamar'), $filename);
        
                KamarPhoto::create([
                    'kamar_id' => $kamar->id,
                    'foto' => 'uploads/kamar/' . $filename,
                    'is_utama' => false
                ]);
            }
        }

        return redirect()->route('kamar.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    // public function edit($id)
    // {
    //     $room = KamarModel::findOrFail($id);
    //     $messes = MessModel::all();
    //     return view('rooms.edit', compact('room', 'messes'));
    // }

    public function edit($id)
    {
        $kamar = KamarModel::with('photos')->findOrFail($id);
        $mess = MessModel::where('status', 1)->get();
        $jabatan=Jabatan::all();
        return response()->json([
            'kamar' => $kamar,
            'mess_list' => $mess,
            'jabatan_list' => $jabatan
        ]);
    }


    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'mess_id' => 'required|exists:messes,id',
    //         'nama_kamar' => 'required|string|max:255',
    //         'kapasitas' => 'required|integer',
    //         'harga' => 'required|numeric',
    //         'fasilitas' => 'nullable|string',
    //         'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
    //     ]);

    //     $room = KamarModel::findOrFail($id);
    //     $data = $request->all();

    //     if ($request->hasFile('foto')) {
    //         $data['foto'] = $request->file('foto')->store('uploads/kamar', 'public');
    //     }

    //     $room->update($data);

    //     return redirect()->route('rooms.index')->with('success', 'Kamar berhasil diperbarui.');
    // }
    public function update(Request $request, $id)
    {
        $request->validate([
            'mess_id' => 'required',
            'nama_kamar' => 'required',
            'kapasitas' => 'required',
            'peruntukan' => 'required',
            'fasilitas' => 'nullable',
            'foto_utama' => 'image|mimes:jpeg,png,jpg|max:2048',
            'foto_pendukung.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // $kamar = KamarModel::findOrFail($id);
        // $kamar->update($request->only('mess_id', 'nama_kamar', 'kapasitas',  'fasilitas'));
        $data = $request->only('mess_id', 'nama_kamar', 'kapasitas', 'fasilitas');
        $data['peruntukan'] = implode(',', $request->peruntukan);

        $kamar = KamarModel::findOrFail($id);
        $kamar->update($data);

        

        if ($request->hasFile('foto_utama')) {
            // Hapus foto lama
            $fotoUtama = KamarPhoto::where('kamar_id', $kamar->id)->where('is_utama', true)->first();
            if ($fotoUtama) {
                $oldPath = public_path($fotoUtama->foto);
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
                $fotoUtama->delete();
            }
        
            // Upload foto baru
            $file = $request->file('foto_utama');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/kamar'), $filename);
        
            KamarPhoto::create([
                'kamar_id' => $kamar->id,
                'foto' => 'uploads/kamar/' . $filename,
                'is_utama' => true
            ]);
        }
        
        // Update Foto Pendukung
        if ($request->hasFile('foto_pendukung')) {
            foreach ($request->file('foto_pendukung') as $file) {
                $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/kamar'), $filename);
        
                KamarPhoto::create([
                    'kamar_id' => $kamar->id,
                    'foto' => 'uploads/kamar/' . $filename,
                    'is_utama' => false
                ]);
            }
        }

        return redirect()->route('kamar.index')->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroyphotokamar($id)
    {
        // dd($id);
        $foto = KamarPhoto::findOrFail($id);
        // dd($foto);

        // Hapus file dari storage
        $path = public_path($foto->foto);
        if (File::exists($path)) {
            File::delete($path);
        }

        // Hapus record dari database
        $foto->delete();

        return response()->json(['message' => 'Foto berhasil dihapus.']);
    }

    public function destroy($id)
    {
        
        $kamar = KamarModel::findOrFail($id);
        $kamar->update([
            'status' => 0,
        ]);
        return redirect()->route('kamar.index')->with('success', 'Kamar berhasil dinonaktifkan.');
    }

    public function aktif($id)
    {
        $kamar = KamarModel::findOrFail($id);

        
        $kamar->update([
            'status' => 1,
        ]);

        return redirect()->route('kamar.index')->with('success', 'Kamar berhasil diaktifkan.');
    }

    // public function getReviews($id)
    // {
    //     $reviews = ReviewModel::whereHas('booking', function ($query) use ($id) {
    //         $query->where('kamar_id', $id);
    //     })
    //     ->select('rating', 'review', 'created_at')
    //     ->orderBy('created_at', 'desc')
    //     ->get();

    //     return response()->json($reviews);
    // }
    public function getReviews($id)
    {
        $reviews = ReviewModel::whereHas('booking', function ($query) use ($id) {
            $query->where('kamar_id', $id);
        })
        ->with(['booking' => function ($query) {
            $query->select('id', 'nama_pemesan','regional');
        }])
        ->orderBy('updated_at', 'desc') // Urutkan dari terbaru
        ->get();

        return response()->json($reviews);
    }


}
