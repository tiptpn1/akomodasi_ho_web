<?php

namespace App\Http\Controllers;

use App\Models\KasKecil;
use App\Models\KasKecilBbm;
use App\Models\KasKecilCc;
use App\Models\KasKecilDriver;
use App\Models\KasKecilGl;
use App\Models\KasKecilGroup;
use App\Models\KasKecilKendaraan;
use App\Models\KasKecilVendor;
use App\Models\KasKecilGlGroup;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class KasKecilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kaskecil = KasKecil::with(['group', 'gl', 'cc', 'bbm', 'kendaraan'])->get();
        $bbm = KasKecilBbm::get();
        $group = KasKecilGroup::get();
        $cc = KasKecilCc::get();
        $gl = KasKecilGl::get();
        $glgroup = KasKecilGlGroup::get();
        $kendaraan = KasKecilKendaraan::get();
        $driver = KasKecilDriver::get();
        $vendor = KasKecilVendor::get();

        $view_data = [
            'kaskecil' => $kaskecil,
            'bbm' => $bbm,
            'cc' => $cc,
            'gl' => $gl,
            'group' => $group,
            'kendaraan' => $kendaraan,
            'driver' => $driver,
            'vendor' => $vendor,
            'glgroup' => $glgroup,
        ];
        return view('kaskecil.index', $view_data);
    }

    public function store(Request $request)
    {
        // Create a new instance of your model (assuming your model is named `Kaskecil`)
        $kaskecil = new Kaskecil();

        // Fill the model with the data from the request
        $kaskecil->nama_pengaju = $request->nama_pengaju;
        $kaskecil->tgl_pengajuan = $request->tgl_pengajuan;
        $kaskecil->id_group = $request->id_group;
        $kaskecil->nomor_gl = $request->nomor_gl;
        $kaskecil->nomor_cc = $request->nomor_cc;
        $kaskecil->id_kendaraan = $request->id_kendaraan;
        $kaskecil->km_awal = $request->km_awal;
        $kaskecil->km_akhir = $request->km_akhir;
        $kaskecil->id_bbm = $request->id_bbm;
        $kaskecil->liter_bensin = $request->liter_bensin;
        $kaskecil->harga_bensin = $request->harga_bensin;
        $kaskecil->nominal = $request->nominal;
        $kaskecil->ppn = $request->ppn;
        $kaskecil->pph = $request->pph;
        $kaskecil->tol = $request->tol;
        $kaskecil->parkir = $request->parkir;
        $kaskecil->biaya_aplikasi = $request->biaya_aplikasi;
        $kaskecil->lain_lain = $request->lain_lain;
        $kaskecil->dibayarkan_oleh = $request->dibayarkan_oleh;
        $kaskecil->tgl_dibayarkan = $request->tgl_dibayarkan;

        // Handle file uploads for `bukti_nota` and `bukti_bayar`
        if ($request->hasFile('bukti_nota')) {
            $file_nota = $request->file('bukti_nota');

            // Create the encrypted filename based on the required fields
            $filename_nota = $this->generateEncryptedFilename($request, 'bukti_nota', $file_nota);

            // Save the file in the specified directory and get the filename
            $file_nota->storeAs('bukti_nota', $filename_nota, 'public');

            // Store only the filename in the database, not the full path
            $kaskecil->bukti_nota = $filename_nota;
        }

        if ($request->hasFile('bukti_bayar')) {
            $file_bayar = $request->file('bukti_bayar');

            // Create the encrypted filename based on the required fields
            $filename_bayar = $this->generateEncryptedFilename($request, 'bukti_bayar', $file_bayar);

            // Save the file in the specified directory and get the filename
            $file_bayar->storeAs('bukti_bayar', $filename_bayar, 'public');

            // Store only the filename in the database, not the full path
            $kaskecil->bukti_bayar = $filename_bayar;
        }

        // Set the `keterangan` field
        $kaskecil->keterangan = $request->keterangan;

        // Save the record
        $kaskecil->save();

        // Redirect back or to a specific route with a success message
        return redirect()->route('kaskecil.index')->with('success', 'Data berhasil disimpan!');
    }

    // Function to generate encrypted filename with a fixed length of 12 characters
    private function generateEncryptedFilename($request, $fieldName, $file)
    {
        // Get the file extension (e.g., jpg, pdf, png, etc.)
        $extension = $file->getClientOriginalExtension();

        // Concatenate the necessary data to form the filename
        $filename = $request->nama_pengaju . '-' .
            $request->id_group . '-' .
            $request->tgl_pengaju . '-' .
            $request->tgl_dibayarkan . '-' .
            $request->nomor_gl . '-' .
            $request->nomor_cc;

        // Prefix the filename based on the field name
        if ($fieldName === 'bukti_nota') {
            $filename = '1-' . $filename;
        } elseif ($fieldName === 'bukti_bayar') {
            $filename = '2-' . $filename;
        }

        // Generate a hash and limit it to 12 characters using md5
        $hashedFilename = substr(md5($filename), 0, 12);

        // Return the final filename with the file extension
        return $hashedFilename . '.' . $extension;
    }

    public function destroy($id)
    {
        // Find the KasKecil record by ID
        $kaskecil = KasKecil::findOrFail($id);

        // Optionally, delete the associated files if they exist
        if ($kaskecil->bukti_nota && Storage::disk('public')->exists('bukti_nota/' . $kaskecil->bukti_nota)) {
            Storage::disk('public')->delete('bukti_nota/' . $kaskecil->bukti_nota);
        }

        if ($kaskecil->bukti_bayar && Storage::disk('public')->exists('bukti_bayar/' . $kaskecil->bukti_bayar)) {
            Storage::disk('public')->delete('bukti_bayar/' . $kaskecil->bukti_bayar);
        }

        // Delete the KasKecil record
        $kaskecil->delete();

        // Redirect back or to a specific route with a success message
        return redirect()->route('kaskecil.index')->with('success', 'Data berhasil dihapus!');
    }

    public function edit($id)
    {
        $data = KasKecil::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {

        // Find the KasKecil record by ID
        $kaskecil = KasKecil::findOrFail($id);

        // Update the model attributes with the new data
        $kaskecil->nama_pengaju = $request->nama_pengaju;
        $kaskecil->tgl_pengajuan = $request->tgl_pengajuan;
        $kaskecil->id_group = $request->id_group;
        $kaskecil->nomor_gl = $request->nomor_gl;
        $kaskecil->nomor_cc = $request->nomor_cc;
        $kaskecil->id_kendaraan = $request->id_kendaraan;
        $kaskecil->km_awal = $request->km_awal;
        $kaskecil->km_akhir = $request->km_akhir;
        $kaskecil->id_bbm = $request->id_bbm;
        $kaskecil->liter_bensin = $request->liter_bensin;
        $kaskecil->harga_bensin = $request->harga_bensin;
        $kaskecil->nominal = $request->nominal;
        $kaskecil->ppn = $request->ppn;
        $kaskecil->pph = $request->pph;
        $kaskecil->tol = $request->tol;
        $kaskecil->parkir = $request->parkir;
        $kaskecil->biaya_aplikasi = $request->biaya_aplikasi;
        $kaskecil->lain_lain = $request->lain_lain;
        $kaskecil->dibayarkan_oleh = $request->dibayarkan_oleh;
        $kaskecil->tgl_dibayarkan = $request->tgl_dibayarkan;


        // Handle file uploads for `bukti_nota` and `bukti_bayar`
        if ($request->hasFile('bukti_nota')) {
            $file_nota = $request->file('bukti_nota');
            // Create the encrypted filename based on the required fields
            $filename_nota = $this->generateEncryptedFilename($request, 'bukti_nota', $file_nota);
            // Save the file in the specified directory and get the filename
            $file_nota->storeAs('bukti_nota', $filename_nota, 'public');
            // Store only the filename in the database, not the full path
            $kaskecil->bukti_nota = $filename_nota;
        }

        if ($request->hasFile('bukti_bayar')) {
            $file_bayar = $request->file('bukti_bayar');
            // Create the encrypted filename based on the required fields
            $filename_bayar = $this->generateEncryptedFilename($request, 'bukti_bayar', $file_bayar);
            // Save the file in the specified directory and get the filename
            $file_bayar->storeAs('bukti_bayar', $filename_bayar, 'public');
            // Store only the filename in the database, not the full path
            $kaskecil->bukti_bayar = $filename_bayar;
        }


        // Update the `keterangan` field
        $kaskecil->keterangan = $request->keterangan;

        // Save the updated record
        $kaskecil->save();

        session()->flash('success', 'Data berhasil diperbarui.');

        // Redirect back with a success message
        return response()->json([
            'redirect_url' => route('kaskecil.index'),
            'message' => 'Data berhasil diperbarui.'
        ]);
    }
}
