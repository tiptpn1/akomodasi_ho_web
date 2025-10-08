<?php

namespace App\Http\Controllers;

use App\Exports\MasterKendaraanExport;
use Illuminate\Http\Request;
use App\Models\MKendaraan;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class MKendaraanController extends Controller
{
    public function index()
    {
        // $kendaraans = MKendaraan::all(); // Mengambil semua data dari tabel kendaraan
        $kendaraans = MKendaraan::where('kendaraan_regional_id',Auth::user()->bagian->regional->id_regional)->get();
        return view('kendaraan.index', compact('kendaraans'));
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'nopol' => 'required|string|max:20|unique:m_kendaraan,nopol',
    //         'tipe_kendaraan' => 'required|string|max:100',
    //         'kepemilikan' => 'required|string|max:100',
    //     ]);

    //     MKendaraan::create([
    //         'kendaraan_regional_id' => Auth::user()->bagian->regional->id_regional,
    //         'nopol' => $request->nopol,
    //         'tipe_kendaraan' => $request->tipe_kendaraan,
    //         'kepemilikan' => $request->kepemilikan,
    //     ]);

    //     return redirect('/masterkendaraan')->with('success', 'Kendaraan berhasil ditambahkan.');
    // }

        public function store(Request $request)
    {
        // Validate the request data, including the new 'foto' field
        $request->validate([
            'nopol' => 'required|string|max:20|unique:m_kendaraan,nopol',
            'tipe_kendaraan' => 'required|string|max:100',
            'kepemilikan' => 'required|string|max:100',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB, common image formats
        ]);

        $fotoFileName = null; // Initialize fotoFileName to null

        // Handle file upload if a foto is present in the request
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            // Generate a unique filename to prevent overwriting
            $fotoFileName = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            // Define the storage path within public directory
            $destinationPath = public_path('uploads/foto_kendaraan');

            // Create the directory if it doesn't exist
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            // Move the uploaded file to the destination path
            $foto->move($destinationPath, $fotoFileName);
        }

        // Create a new MKendaraan record in the database
        MKendaraan::create([
            'kendaraan_regional_id' => Auth::user()->bagian->regional->id_regional,
            'nopol' => $request->nopol,
            'tipe_kendaraan' => $request->tipe_kendaraan,
            'kepemilikan' => $request->kepemilikan,
            'foto' => $fotoFileName, // Save the filename (or null if no photo uploaded)
        ]);

        return redirect('/masterkendaraan')->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = MKendaraan::findOrFail($id);
        return response()->json($data);
    }

    // public function update(Request $request, $id)
    // {

    //     // Find the KasKecil record by ID
    //     $masterkendaraan = MKendaraan::findOrFail($id);


    //     $masterkendaraan->nopol = $request->nopol1;
    //     $masterkendaraan->tipe_kendaraan = $request->tipe_kendaraan1;
    //     $masterkendaraan->kepemilikan = $request->kepemilikan1;

    //     // Save the updated record
    //     $masterkendaraan->save();

    //     session()->flash('success', 'Data berhasil diperbarui.');

    //     // Redirect back with a success message
    //     return response()->json([
    //         'redirect_url' => url('/masterkendaraan'),
    //         'message' => 'Data berhasil diperbarui.'
    //     ]);
    // }


     public function update(Request $request, $id)
    {
        // Temukan record MKendaraan berdasarkan ID
        $masterkendaraan = MKendaraan::findOrFail($id);

        // Validasi data request, termasuk foto
        $request->validate([
            'nopol1' => [
                'required',
                'string',
                'max:20',
                // Pastikan nopol unik kecuali untuk record yang sedang diedit
                Rule::unique('master_kendaraan', 'nopol')->ignore($masterkendaraan->id_kendaraan, 'id_kendaraan'),
            ],
            'tipe_kendaraan1' => 'required|string|max:100',
            'kepemilikan1' => 'required|string|max:100',
            'foto_edit' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Input file untuk edit
        ]);

        // Update data dasar
        $masterkendaraan->nopol = $request->nopol1;
        $masterkendaraan->tipe_kendaraan = $request->tipe_kendaraan1;
        $masterkendaraan->kepemilikan = $request->kepemilikan1;

        // Tangani upload foto jika ada foto baru
        if ($request->hasFile('foto_edit')) {
            // Hapus foto lama jika ada
            if ($masterkendaraan->foto && File::exists(public_path('uploads/foto_kendaraan/' . $masterkendaraan->foto))) {
                File::delete(public_path('uploads/foto_kendaraan/' . $masterkendaraan->foto));
            }

            $foto = $request->file('foto_edit');
            $fotoFileName = time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            $destinationPath = public_path('uploads/foto_kendaraan');

            // Pastikan direktori ada
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }

            // Pindahkan file baru
            $foto->move($destinationPath, $fotoFileName);

            // Perbarui nama file foto di database
            $masterkendaraan->foto = $fotoFileName;
        }
        // Jika tidak ada foto baru diupload, biarkan foto yang sudah ada (tidak perlu else block)

        // Simpan record yang diperbarui
        $masterkendaraan->save();

        session()->flash('success', 'Data berhasil diperbarui.');

        // Redirect kembali dengan pesan sukses
        return response()->json([
            'redirect_url' => url('/masterkendaraan'),
            'message' => 'Data berhasil diperbarui.'
        ]);
    }


    // public function destroy($id)
    // {
    //     try {
    //         $masterkendaraan = MKendaraan::findOrFail($id);
    //         $masterkendaraan->delete();
    //         return redirect()->to(url('/masterkendaraan'))->with('success', 'Data berhasil dihapus!');
    //     } catch (\Exception $e) {
    //         return redirect()->to(url('/masterkendaraan'))->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    //     }
    // }

        public function destroy($id)
    {
        try {
            $masterkendaraan = MKendaraan::findOrFail($id);

            // --- Logika untuk menghapus foto fisik ---
            // Periksa apakah ada nama file foto yang terkait dengan kendaraan ini
            if ($masterkendaraan->foto) {
                // Buat jalur lengkap ke file foto
                $fotoPath = public_path('uploads/foto_kendaraan/' . $masterkendaraan->foto);

                // Periksa apakah file foto benar-benar ada sebelum mencoba menghapusnya
                if (File::exists($fotoPath)) {
                    File::delete($fotoPath); // Hapus file fisik
                    \Log::info('Foto kendaraan berhasil dihapus: ' . $fotoPath); // Opsional: log keberhasilan
                } else {
                    \Log::warning('Foto kendaraan tidak ditemukan untuk dihapus: ' . $fotoPath); // Opsional: log jika tidak ditemukan
                }
            }
            // --- Akhir logika penghapusan foto fisik ---

            // Hapus record dari database
            $masterkendaraan->delete();

            return redirect()->to(url('/masterkendaraan'))->with('success', 'Data kendaraan dihapus!');
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Gagal menghapus kendaraan ID ' . $id . ': ' . $e->getMessage());

            return redirect()->to(url('/masterkendaraan'))->with('error', 'Terjadi kesalahan saat menghapus data kendaraan: ' . $e->getMessage());
        }
    }


    public function export(Request $request)
    {
        // $data = MKendaraan::all(); // Ambil semua data kendaraan
        $data = MKendaraan::where('kendaraan_regional_id',Auth::user()->bagian->regional->id_regional)->get();
        return Excel::download(new MasterKendaraanExport($data), 'masterkendaraan_export.xlsx');
    }
}
