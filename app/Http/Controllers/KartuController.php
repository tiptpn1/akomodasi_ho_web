<?php

namespace App\Http\Controllers;

use App\Exports\MSExport;
use App\Models\Bagian;
use Illuminate\Http\Request;
use App\Models\SendVicon;
use App\Models\Kartu;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Storage;

class KartuController extends Controller
{
    public function index(Request $request)
    {
        $bagian=Auth::user()->master_nama_bagian_id;
        $divisi = Bagian::find($bagian);
        if (in_array(Auth::user()->master_user_nama, ['asisten_ga', 'kasubdiv_ga'])) {
            // Jika user adalah asisten_ga atau kasubdiv_ga, tampilkan semua data
            $kartu = Kartu::get();
        } else {
            // Jika bukan, tampilkan hanya data sesuai bagian user
            $kartu = Kartu::where('divisi', $divisi->master_bagian_nama)->get();
        }
        $get_divisi=Bagian::get();

        $view_data = [
            'kartu' => $kartu,
            'divisi'     =>$divisi->master_bagian_nama,
            'get_divisi'    =>$get_divisi,
        ];
        return view('kartu.index', $view_data);
        // return view('konsumsi.index', compact('konsumsi', 'start', 'bagians', 'request_status'));
    }

    public function store(Request $request)
    {
        dd($request->all());
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_pengaju' => 'required|string|max:255',
            'nik'           => 'required|string|max:50',
            'divisi'        => 'required|string|max:255',
            'ck_lift'       => 'nullable|in:1',
            'ck_parkir'     => 'nullable|in:1',
            'jenis_kendaraan1' => 'nullable|required_if:ck_parkir,1|string|in:Mobil,Motor',
            'nopol1'        => 'nullable|required_if:ck_parkir,1|string|max:20',
            'stnk1'         => 'nullable|required_if:ck_parkir,1|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'jenis_kendaraan2' => 'nullable|string|in:Mobil,Motor',
            'nopol2'        => 'nullable|string|max:20',
            'stnk2'         => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'ktp'           => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'memo'          => 'required|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'stnk1.mimes'   => 'STNK 1 harus berupa file bertipe: jpeg, png, jpg, pdf.',
            'stnk2.mimes'   => 'STNK 2 harus berupa file bertipe: jpeg, png, jpg, pdf.',
            'ktp.mimes'     => 'File KTP harus berupa file bertipe: jpeg, png, jpg, pdf.',
            'memo.mimes'    => 'File Memo harus berupa file bertipe: jpeg, png, jpg, pdf.',
            'ktp.max'       => 'Ukuran file KTP maksimal 2MB.',
            'memo.max'      => 'Ukuran file Memo maksimal 2MB.',
            'ck_lift.required' => 'Pilih Lift atau Parkir.',
            'ck_parkir.required' => 'Pilih Lift atau Parkir.',
            'stnk1.max'     => 'Ukuran file STNK 1 maksimal 2MB.',
            'stnk2.max'     => 'Ukuran file STNK 2 maksimal 2MB.',
        ]);
        
        // Jika validasi gagal
        if ($validator->fails()) {
            // Mengambil semua pesan kesalahan
            $errors = $validator->errors();
            // Redirect kembali dengan pesan error
            return redirect()->route('kartu.index')->with('error',$errors);
            // return redirect()->back()->withErrors($errors)->withInput();
        }
        if (!$request->has('ck_lift') && !$request->has('ck_parkir')) {
            return redirect()->route('kartu.index')->with('error','minimal centang salah satu pilihan');
        }
    
        // Jika validasi berhasil, lanjutkan dengan logika penyimpanan data
        // dd('Validasi berhasil');
       
        // dd($validated); 
        // dd( $validated);
        // Buat instance model
        $validated = $validator->validated();
        $kartu = new Kartu();
        $kartu->nama_pengaju = $validated['nama_pengaju'];
        $kartu->nik_karyawan = $validated['nik'];
        $kartu->divisi = $validated['divisi'];
        $kartu->lift = $request->has('ck_lift') ? 1 : 0;
        $kartu->parkir = $request->has('ck_parkir') ? 1 : 0;

        // Data kendaraan
        $kartu->k1 = $validated['jenis_kendaraan1'] ?? null;
        $kartu->k2 = $validated['jenis_kendaraan2'] ?? null;
        $kartu->nopol1 = $validated['nopol1'] ?? null;
        $kartu->nopol2 = $validated['nopol2'] ?? null;

        // Upload file
        try {
            if ($request->hasFile('stnk1')) {
                $this->validateFile($request->file('stnk1'), 'stnk1');
                $kartu->stnk1_file = $request->file('stnk1')->store('uploads/stnk', 'public');
            }
            if ($request->hasFile('stnk2')) {
                $this->validateFile($request->file('stnk2'), 'stnk2');
                $kartu->stnk2_file = $request->file('stnk2')->store('uploads/stnk', 'public');
            }
            if ($request->hasFile('ktp')) {
                $this->validateFile($request->file('ktp'), 'ktp');
                $kartu->ktp_file = $request->file('ktp')->store('uploads/ktp', 'public');
            }
            if ($request->hasFile('memo')) {
                $this->validateFile($request->file('memo'), 'memo');
                $kartu->memo_file = $request->file('memo')->store('uploads/memo', 'public');
            }
        } catch (\Exception $e) {
            // Redirect with error message if file type is incorrect
            // return redirect()->back()->with('error', $e->getMessage());
            return redirect()->route('kartu.index')->with('error', 'Data berhasil disimpan!');
        }

        // Status default
        $kartu->status_lift = $kartu->lift ? 'Pengajuan' : null;
        $kartu->status_parkir = $kartu->parkir ? 'Pengajuan' : null;
        $kartu->created_at = now();
        $kartu->updated_at = now();

        // Simpan data
        $kartu->save();

        // Redirect dengan pesan sukses
        return redirect()->route('kartu.index')->with('success', 'Data berhasil disimpan!');
    }
    
    protected function validateFile($file, $fileName)
    {
        $validMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        if (!in_array($file->getMimeType(), $validMimeTypes)) {
            throw new \Exception("The $fileName file must be a file of type: jpeg, png, jpg, pdf.");
        }
    }


    public function edit($id)
    {
        $data = Kartu::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        dd($request->all());
        // dd('dapat');
        // Find the KasKecil record by ID
        // $makansiang = MakanSiang::findOrFail($id);

        // // Update the model attributes with the new data
        // $makansiang->tgl_permintaan = $request->tgl_pengajuan1;
        // $makansiang->nama_pic = $request->nama_pengaju1;
        // $makansiang->divisi = $request->divisi1;
        // $makansiang->kadiv = $request->kadiv1;
        // $makansiang->jlh_karyawan = $request->jlh_karyawan1;
        // $makansiang->jlh_makan = $request->jlh_makansiang1;
        // $makansiang->username = Auth::user()->master_user_nama;
        // $makansiang->apprv = Auth::user()->master_user_nama;


        // // Save the updated record
        // $makansiang->save();

        // session()->flash('success', 'Data berhasil diperbarui.');

        // Redirect back with a success message
        // return response()->json([
        //     'redirect_url' => route('makansiang.index'),
        //     'message' => 'Data berhasil diperbarui.'
        // ]);

        // Validasi input
        dd($request->hasFile('memo1'));
        $validator = Validator::make($request->all(), [
            'nama_pengaju1' => 'required|string|max:255',
            'nik1'           => 'required|string|max:50',
            'divisi1'        => 'required|string|max:255',
            'ck_lift1'       => 'nullable|in:1',
            'ck_parkir1'     => 'nullable|in:1',
            'jenis_kendaraan3' => 'nullable|required_if:ck_parkir,1|string|in:Mobil,Motor',
            'nopol3'        => 'nullable|required_if:ck_parkir,1|string|max:20',
            'stnk3'         => 'nullable|required_if:ck_parkir,1|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'jenis_kendaraan4' => 'nullable|string|in:Mobil,Motor',
            'nopol4'        => 'nullable|string|max:20',
            'stnk4'         => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'ktp1'           => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'memo1'          => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ], [
            'stnk3.mimes'   => 'STNK 1 harus berupa file bertipe: jpeg, png, jpg, pdf.',
            'stnk4.mimes'   => 'STNK 2 harus berupa file bertipe: jpeg, png, jpg, pdf.',
            'ktp1.mimes'     => 'File KTP harus berupa file bertipe: jpeg, png, jpg, pdf.',
            'memo1.mimes'    => 'File Memo harus berupa file bertipe: jpeg, png, jpg, pdf.',
            'stnk3.max'     => 'Ukuran file STNK 1 maksimal 2MB.',
            'stnk4.max'     => 'Ukuran file STNK 2 maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            // return redirect()->route('kartu.index', $id)->withErrors($errors)->withInput();
            // dd( $errors);
            return redirect()->route('kartu.index', $id)->with('error', $errors);
        }

        // if (!$request->has('ck_lift1') && !$request->has('ck_parkir1')) {
        //     dd(!$request->has('ck_parkir'));
        //     return redirect()->route('kartu.index', $id)->with('error', 'Minimal centang salah satu pilihan.');
        // }

        // // Ambil data yang akan diperbarui
        $kartu = Kartu::findOrFail($id);
        $validated = $validator->validated();

        // // Update data utama
        $kartu->nama_pengaju = $validated['nama_pengaju1'];
        $kartu->nik_karyawan = $validated['nik1'];
        $kartu->divisi = $validated['divisi1'];
        $kartu->lift = $request->has('ck_lift1') ? 1 : 0;
        $kartu->parkir = $request->has('ck_parkir1') ? 1 : 0;

        // // Update data kendaraan
        $kartu->k1 = $validated['jenis_kendaraan3'] ?? null;
        $kartu->k2 = $validated['jenis_kendaraan4'] ?? null;
        $kartu->nopol1 = $validated['nopol3'] ?? null;
        $kartu->nopol2 = $validated['nopol4'] ?? null;
        // $kartu->save();
        // Update file jika ada file baru yang diunggah
        // try {
            if ($request->hasFile('stnk3')) {
                dd('ceka');
                $this->validateFile($request->file('stnk3'), 'stnk3');
                if ($kartu->stnk1_file) {
                    dd('cek1');
                    Storage::disk('public')->delete($kartu->stnk1_file);
                }
                $kartu->stnk1_file = $request->file('stnk3')->store('uploads/stnk', 'public');
            }
            if ($request->hasFile('stnk4')) {
                dd('cekb');
                $this->validateFile($request->file('stnk4'), 'stnk4');
                if ($kartu->stnk2_file) {
                    dd('cek2');
                    Storage::disk('public')->delete($kartu->stnk2_file);
                }
                $kartu->stnk2_file = $request->file('stnk4')->store('uploads/stnk', 'public');
            }
            if ($request->hasFile('ktp1')) {
                dd('cekc');
                $this->validateFile($request->file('ktp1'), 'ktp1');
                if ($kartu->ktp_file) {
                    dd('cek3');
                    Storage::disk('public')->delete($kartu->ktp_file);
                }
                $kartu->ktp_file = $request->file('ktp1')->store('uploads/ktp', 'public');
            }
            if ($request->hasFile('memo1')) {
                dd('cekd');
                $this->validateFile($request->file('memo1'), 'memo1');
                if ($kartu->memo_file) {
                    dd('cek4');
                    Storage::disk('public')->delete($kartu->memo_file);
                }
                $kartu->memo_file = $request->file('memo1')->store('uploads/memo', 'public');
            }
        // } catch (\Exception $e) {
        //     return redirect()->route('kartu.index', $id)->with('error', 'Terjadi kesalahan saat mengunggah file.');
        // }

        // Update status
        $kartu->status_lift = $kartu->lift ? 'Pengajuan' : null;
        $kartu->status_parkir = $kartu->parkir ? 'Pengajuan' : null;
        $kartu->updated_at = now();
        $kartu->nama_pengaju=$request->nama_pengaju1;
        // Simpan data
        $kartu->save();

        session()->flash('success', 'Data berhasil diperbarui.');

        // Redirect back with a success message
        return response()->json([
            'redirect_url' => route('kartu.index'),
            'message' => 'Data berhasil diperbarui.'
        ]);


    }

    public function destroy(Request $request, $id)
    {
        $kartu = Kartu::findOrFail($id);
        // dd($kartu->status_lift);
        // dd($kartu->status_parkir);
        if($kartu->status_lift=='Pengajuan')
        {
            $kartu->status_lift='Batal';
        }
        if($kartu->status_parkir=='Pengajuan')
        {
            $kartu->status_parkir='Batal';
        }

        // Update status menjadi 0 (dibatalkan)
        // $kartu->status = 0;
        $kartu->apprv_by = Auth::user()->master_user_nama;
        $kartu->save();

        return redirect()->route('kartu.index')->with('success', 'Permintaan kartu dibatalkan.');
    }

    public function export(Request $request)
    {
        // dd('test');
        // Get the filter parameters from the request
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');
        $divisi = $request->input('id_divisi');
        $status = $request->input('status');

        // dd($id_group);

        // Query the data based on filters
        $makansiang = MakanSiang::query();

        // $kaskecil = Kaskecil::query();

        if ($tgl_awal) {
            $makansiang->where('tgl_permintaan', '>=', $tgl_awal);
        }

        if ($tgl_akhir) {
            $makansiang->where('tgl_permintaan', '<=', $tgl_akhir);
        }

        if ($divisi) {
            if($divisi<>'all')
            {
                $makansiang->where('divisi', '=', $divisi);
            }
            
           
        }

        if ($status) {
            if($status<>'all')
            {
                $makansiang->where('status', '=', $status);
            }
        }


        // Fetch the data
        $data = $makansiang->get();
        // dd($data);
        // dd($data->first()->group, $data->first()->gl, $data->first()->cc);


        // Export logic (using Laravel Excel)
        return Excel::download(new MSExport($data), 'makansiang_export.xlsx');
    }

    public function approve($id)
    {
        $makansiang = MakanSiang::find($id);
        $makansiang->status = 2; // Approved
        $makansiang->apprv = Auth::user()->master_user_nama;
        $makansiang->save();

        return redirect()->route('makansiang.index')->with('success', 'Data approved.');
    }

    public function reject($id)
    {
        $makansiang = MakanSiang::find($id);
        $makansiang->status = 3; // Rejected
        $makansiang->apprv = Auth::user()->master_user_nama;
        $makansiang->save();

        return redirect()->route('makansiang.index')->with('error', 'Data rejected.');
    }


    
}
