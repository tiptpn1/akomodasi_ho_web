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
use App\Exports\KaskecilExport; 
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
        $bbm = KasKecilBbm::get();
        $group = KasKecilGroup::get();
        $cc = KasKecilCc::get();
        $gl = KasKecilGl::get();
        $glgroup = KasKecilGlGroup::get();
        $kendaraan = KasKecilKendaraan::get();
        $driver = KasKecilDriver::get();
        $vendor = KasKecilVendor::get();

        $view_data = [
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

    /**
     * Get data for DataTables server-side processing.
     */
    public function data()
    {
        $data = KasKecil::with(['group', 'gl', 'cc', 'bbm', 'kendaraan'])
                ->select('d_kaskecil.*')
                ->orderBy('id_kaskecil', 'desc');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('aksi', function($row){
                $userHakAkses = Auth::user()->master_hak_akses_id;
                if (in_array($userHakAkses, [5, 6])) {
                    return '-';
                }
                
                $editBtn = '<button type="button" class="btn btn-sm btn-info btn-edit" data-toggle="modal" data-target="#edit" data-id="'.$row->id_kaskecil.'"><i class="fa fa-pencil" style="color: white;"></i></button>';
                
                $deleteUrl = route('kaskecil.destroy', $row->id_kaskecil);
                $csrf = csrf_field();
                $method = method_field('DELETE');
                
                $deleteBtn = '
                    <form action="'.$deleteUrl.'" method="POST" style="display:inline;" onsubmit="return confirm(\'Apakah yakin menghapus data ini?\')">
                        '.$csrf.'
                        '.$method.'
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                    </form>
                ';

                return '<div class="btn-group" role="group">'.$editBtn.' '.$deleteBtn.'</div>';
            })
            ->addColumn('nama_group', function($row){
                return $row->group->nama_group ?? '-';
            })
            ->addColumn('gl_display', function($row){
                return ($row->gl->nomor_gl ?? '-') . ' - ' . ($row->gl->nama_gl ?? '-');
            })
            ->addColumn('cc_display', function($row){
                return ($row->cc->nomor_cc ?? '-') . ' - ' . ($row->cc->nama_cc ?? '-');
            })
            ->addColumn('nominal_formatted', function($row){
                return $row->nominal !== null ? 'Rp. ' . number_format($row->nominal, 2, ',', '.') : '-';
            })
            ->addColumn('kendaraan_display', function($row){
                return ($row->kendaraan->nopol ?? '-') . ' - ' . ($row->kendaraan->tipe_kendaraan ?? '-');
            })
            ->addColumn('jumlah_km', function($row){
                return ($row->km_akhir ?? 0) - ($row->km_awal ?? 0);
            })
            ->addColumn('nama_bbm', function($row){
                return $row->bbm->nama_bbm ?? '-';
            })
            ->addColumn('harga_bensin_formatted', function($row){
                return $row->harga_bensin !== null ? 'Rp. ' . number_format($row->harga_bensin, 2, ',', '.') : '-';
            })
            ->addColumn('tol_formatted', function($row){
                return $row->tol !== null ? 'Rp. ' . number_format($row->tol, 2, ',', '.') : '-';
            })
            ->addColumn('parkir_formatted', function($row){
                return $row->parkir !== null ? 'Rp. ' . number_format($row->parkir, 2, ',', '.') : '-';
            })
            ->addColumn('ppn_formatted', function($row){
                return $row->ppn !== null ? 'Rp. ' . number_format($row->ppn, 2, ',', '.') : '-';
            })
            ->addColumn('pph_formatted', function($row){
                return $row->pph !== null ? 'Rp. ' . number_format($row->pph, 2, ',', '.') : '-';
            })
            ->addColumn('biaya_aplikasi_formatted', function($row){
                return $row->biaya_aplikasi !== null ? 'Rp. ' . number_format($row->biaya_aplikasi, 2, ',', '.') : '-';
            })
            ->addColumn('lain_lain_formatted', function($row){
                return $row->lain_lain !== null ? 'Rp. ' . number_format($row->lain_lain, 2, ',', '.') : '-';
            })
            ->addColumn('bukti_nota_link', function($row){
                if ($row->bukti_nota) {
                    return '<a href="' . route('kaskecil.bukti', basename($row->bukti_nota)) . '" target="_blank" class="btn btn-sm btn-primary">Lihat</a>';
                }
                return '-';
            })
            ->addColumn('bukti_bayar_link', function($row){
                if ($row->bukti_bayar) {
                    return '<a href="' . route('kaskecil.bukti', basename($row->bukti_bayar)) . '" target="_blank" class="btn btn-sm btn-primary">Lihat</a>';
                }
                return '-';
            })
            ->addColumn('total_biaya', function($row){
                $total = ($row->nominal ?? 0) + ($row->ppn ?? 0) + ($row->pph ?? 0) + ($row->tol ?? 0) + ($row->parkir ?? 0) + ($row->biaya_aplikasi ?? 0) + ($row->lain_lain ?? 0) + ($row->harga_bensin ?? 0);
                return 'Rp. ' . number_format($total, 2, ',', '.');
            })
            ->rawColumns(['aksi', 'bukti_nota_link', 'bukti_bayar_link'])
            ->make(true);
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

    public function export(Request $request)
    {
        // Get the filter parameters from the request
        $tgl_pengajuan_awal = $request->input('tgl_pengajuan_awal');
        $tgl_pengajuan_akhir = $request->input('tgl_pengajuan_akhir');
        $id_group = $request->input('id_group');
        $nomor_gl = $request->input('nomor_gl2');
        $nomor_cc = $request->input('nomor_cc');
        $tgl_dibayarkan = $request->input('tgl_dibayarkan');

        // dd($id_group);

        // Query the data based on filters
        $kaskecil = KasKecil::with(['group', 'gl', 'cc', 'bbm', 'kendaraan']);

        // $kaskecil = Kaskecil::query();

        if ($tgl_pengajuan_awal) {
            $kaskecil->where('tgl_pengajuan', '>=', $tgl_pengajuan_awal);
        }

        if ($tgl_pengajuan_akhir) {
            $kaskecil->where('tgl_pengajuan', '<=', $tgl_pengajuan_akhir);
        }

        if ($id_group) {
            $kaskecil->whereHas('group', function ($query) use ($id_group) {
                $query->where('id_group', '=', $id_group);
            });
        }

        if ($nomor_gl) {
            $kaskecil->whereHas('gl', function ($query) use ($nomor_gl) {
                $query->where('nomor_gl', 'like', "%$nomor_gl%")
                    ->orWhere('nama_gl', 'like', "%$nomor_gl%");
            });
        }

        if ($nomor_cc) {
            $kaskecil->whereHas('cc', function ($query) use ($nomor_cc) {
                $query->where('nomor_cc', 'like', "%$nomor_cc%")
                    ->orWhere('nama_cc', 'like', "%$nomor_cc%");
            });
        }

        if ($tgl_dibayarkan) {
            $kaskecil->where('tgl_dibayarkan', '=', $tgl_dibayarkan);
        }

        // Fetch the data
        $data = $kaskecil->get();
        // dd($data);
        // dd($data->first()->group, $data->first()->gl, $data->first()->cc);


        // Export logic (using Laravel Excel)
        return Excel::download(new KaskecilExport($data), 'kaskecil_export.xlsx');
    }
}
