<?php

namespace App\Http\Controllers;

use App\Exports\KonsumsiExport;
use App\Models\Bagian;
use Illuminate\Http\Request;
use App\Models\SendVicon;
use App\Models\Konsumsi;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class KonsumsiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil tipe agenda dari URL, default-nya adalah 'hari_ini'
        $tipeKonsumsi = $request->input('tipe_konsumsi', 'hari_ini');


            // BARU: Tentukan teks untuk caption berdasarkan tipe agenda
    if ($tipeKonsumsi == 'hari_ini') {
        $caption = 'Daftar Konsumsi Hari Ini';
    } else {
        $caption = 'Daftar Semua Konsumsi';
    }

        $bagian_reg = Bagian::where('master_bagian_id', Auth::user()->master_nama_bagian_id)
        ->orderBy('master_bagian_id', 'desc')
        ->first();
        
        if (Auth::user()->master_user_nama == 'kasubdiv_ga') {
            $query = DB::table('konsumsi')
                ->join('sendvicon', 'konsumsi.id_sendvicon', '=', 'sendvicon.id')
                ->join('master_bagian', 'sendvicon.bagian_id', '=', 'master_bagian.master_bagian_id')
                ->whereIn('konsumsi.status', [1, 2])
                ->when($tipeKonsumsi == 'hari_ini', function ($q) {
            return $q->whereDate('sendvicon.tanggal', today());
        })
                ->where('master_bagian.bagian_regional_id', $bagian_reg->bagian_regional_id)
                ->select(
                    'konsumsi.id as konsumsi_id',
                    'konsumsi.keterangan as konsumsi_keterangan',
                    'sendvicon.id as sendvicon_id',
                    'sendvicon.keterangan as sendvicon_keterangan',
                    'konsumsi.status as status_konsumsi',
                    'konsumsi.*',
                    'sendvicon.*',
                    'master_bagian.*'
                );

            // dd($konsumsi);
        } else if (Auth::user()->master_user_nama == 'kadiv_ga') {
            $query = DB::table('konsumsi')
                ->join('sendvicon', 'konsumsi.id_sendvicon', '=', 'sendvicon.id')
                ->join('master_bagian', 'sendvicon.bagian_id', '=', 'master_bagian.master_bagian_id')
                ->where('konsumsi.konsumsi_kirim', 2)
                ->whereIn('konsumsi.status', [2, 3])
                ->when($tipeKonsumsi == 'hari_ini', function ($q) {
            return $q->whereDate('sendvicon.tanggal', today());
        })
                ->where('master_bagian.bagian_regional_id', $bagian_reg->bagian_regional_id)
                ->select(
                    'konsumsi.id as konsumsi_id',
                    'konsumsi.keterangan as konsumsi_keterangan',
                    'sendvicon.id as sendvicon_id',
                    'sendvicon.keterangan as sendvicon_keterangan',
                    'konsumsi.status as status_konsumsi',
                    'konsumsi.*',
                    'sendvicon.*',
                    'master_bagian.*'
                );
        } else {
            $query = DB::table('konsumsi')
                ->join('sendvicon', 'konsumsi.id_sendvicon', '=', 'sendvicon.id')
                ->join('master_bagian', 'sendvicon.bagian_id', '=', 'master_bagian.master_bagian_id')
                ->when($tipeKonsumsi == 'hari_ini', function ($q) {
            return $q->whereDate('sendvicon.tanggal', today());
        })
                ->where('master_bagian.bagian_regional_id', $bagian_reg->bagian_regional_id)
                ->select(
                    'konsumsi.id as konsumsi_id',
                    'konsumsi.keterangan as konsumsi_keterangan',
                    'sendvicon.id as sendvicon_id',
                    'sendvicon.keterangan as sendvicon_keterangan',
                    'konsumsi.status as status_konsumsi',
                    'konsumsi.*',
                    'sendvicon.*',
                    'master_bagian.*'
                );
        };

        $request_status = [0, 1, 2];

        /**
         * There is 2 filters used
         * a. filter with request page. This filter used for handle pagination
         * b. filter without request page. This filter used for handle form search
         */
        if ($request->page) {
            // filter based tanggal_mulai
            if (Session::has('tanggal_mulai')) {
                $tanggal = implode('-', array_reverse(explode('-', Session::get('tanggal_mulai'))));
                $query->where('tanggal', '>=', $tanggal);
            }

            // filter based tanggal_akhir
            if (Session::has('tanggal_akhir')) {
                $tanggal = implode('-', array_reverse(explode('-', Session::get('tanggal_akhir'))));
                $query->where('tanggal', '<=', $tanggal);
            }

            // filter based bagian
            if (Session::has('bagian')) {
                $query->whereIn('master_bagian.master_bagian_id', Session::get('bagian'));
            }

            // filter based posisi
            if (Session::has('posisi') != '') {
                if (Session::get('posisi') == 'kadiv') {
                    $query->whereIn('konsumsi.status', [2, 3]);
                }

                if (Session::get('posisi') == 'kasubdiv') {
                    $query->where('konsumsi.konsumsi_kirim', 1)
                        ->whereIn('konsumsi.status', [1, 2]);
                }

                if (Session::get('posisi') == 'asisten') {
                    $query->where('konsumsi.konsumsi_kirim', 0)
                        ->whereIn('konsumsi.status', [0, 1]);
                }
            }

            if (Session::has('status')) {
                $request_status = Session::get('status') != '' ? [Session::get('status')] : [0, 1, 2];
            }

            if (Session::has('acara')) {
                $query->where('sendvicon.acara', 'like', '%' . Session::get('acara') . '%');
            }
        } else {
            // reset filter
            Session::remove('tanggal_mulai');
            Session::remove('tanggal_akhir');
            Session::remove('bagian');
            Session::remove('status');
            Session::remove('posisi');
            Session::remove('acara');

            // filter based tanggal_mulai
            if ($request->tanggal_mulai) {
                $tanggal = implode('-', array_reverse(explode('-', $request->tanggal_mulai)));
                $query->where('tanggal', '>=', $tanggal);
                Session::put('tanggal_mulai', $request->tanggal_mulai);
            }

            // filter based tanggal_akhir
            if ($request->tanggal_akhir) {
                $tanggal = implode('-', array_reverse(explode('-', $request->tanggal_akhir)));
                $query->where('tanggal', '<=', $tanggal);
                Session::put('tanggal_akhir', $request->tanggal_akhir);
            }

            // filter based bagian
            if ($request->bagian) {
                $query->whereIn('master_bagian.master_bagian_id', $request->bagian);
                Session::put('bagian',  $request->bagian);
            }

            // filter based posisi
            if ($request->posisi !== null) {
                if ($request->posisi == 'kadiv') {
                    $query->whereIn('konsumsi.status', [2, 3]);
                }

                if ($request->posisi == 'kasubdiv') {
                    $query->where('konsumsi.konsumsi_kirim', 1)
                        ->whereIn('konsumsi.status', [1, 2]);
                }

                if ($request->posisi == 'asisten') {
                    $query->where('konsumsi.konsumsi_kirim', 0)
                        ->whereIn('konsumsi.status', [0, 1]);
                }

                Session::put('posisi', $request->posisi);
            }

            // filter based status
            if ($request->status != '') {
                $request_status = [$request->status];
                Session::put('status', $request->status);
            }

            // filter acara
            if ($request->acara != '') {
                $query->where('sendvicon.acara', 'like', '%' . $request->acara . '%');
                Session::put('acara', $request->acara);
            }
        }

        $konsumsi = $query->paginate(10);

        $start = isset($_GET['page']) ? ($_GET['page'] - 1) * 10 : 0;


        $bagians = Bagian::where('master_bagian.bagian_regional_id', $bagian_reg->bagian_regional_id)->get();

        // $konsumsi = Konsumsi::with(['sendVicon.bagian'])->get();
        return view('konsumsi.index', compact('konsumsi', 'start', 'bagians', 'request_status', 'caption'));
    }

    public function create()
    {
        //
    }

    // Menyimpan data konsumsi baru
    public function store(Request $request)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $konsumsi = Konsumsi::find($id);
        $konsumsi->m_pagi = $request->input('makan.pagi', $konsumsi->m_pagi);
        $konsumsi->m_siang = $request->input('makan.siang', $konsumsi->m_siang);
        $konsumsi->m_malam = $request->input('makan.malam', $konsumsi->m_malam);

        $konsumsi->s_pagi = $request->input('snack.pagi', $konsumsi->s_pagi);
        $konsumsi->s_siang = $request->input('snack.siang', $konsumsi->s_siang);
        $konsumsi->s_sore = $request->input('snack.sore', $konsumsi->s_sore);

        // Update biaya
        $konsumsi->biaya_m_pagi = $request->input('biaya_m_pagi', $konsumsi->biaya_m_pagi);
        $konsumsi->biaya_m_siang = $request->input('biaya_m_siang', $konsumsi->biaya_m_siang);
        $konsumsi->biaya_m_malam = $request->input('biaya_m_malam', $konsumsi->biaya_m_malam);
        $konsumsi->biaya_s_pagi = $request->input('biaya_s_pagi', $konsumsi->biaya_s_pagi);
        $konsumsi->biaya_s_siang = $request->input('biaya_s_siang', $konsumsi->biaya_s_siang);
        $konsumsi->biaya_s_sore = $request->input('biaya_s_sore', $konsumsi->biaya_s_sore);

        $konsumsi->keterangan = $request->input('keterangan', $konsumsi->keterangan);
        $konsumsi->biaya_lain = $request->input('biaya_lain', $konsumsi->biaya_lain);
        $konsumsi->status_batal_m_pagi = $request->input('status_batal_m_pagi', $konsumsi->status_batal_m_pagi); // default $konsumsi->status_batal_m_pagi jika tidak ada
        $konsumsi->status_batal_m_siang = $request->input('status_batal_m_siang', $konsumsi->status_batal_m_siang);
        $konsumsi->status_batal_m_malam = $request->input('status_batal_m_malam', $konsumsi->status_batal_m_malam);
        $konsumsi->status_batal_s_pagi = $request->input('status_batal_s_pagi', $konsumsi->status_batal_s_pagi);
        $konsumsi->status_batal_s_siang = $request->input('status_batal_s_siang', $konsumsi->status_batal_s_siang);
        $konsumsi->status_batal_s_sore = $request->input('status_batal_s_sore', $konsumsi->status_batal_s_sore);

        // Save the updated konsumsi
        $konsumsi->save();

        return redirect()->route('konsumsi.index')->with('success', 'Berhasil diupdate');
    }
    public function approve($id)
    {
        // Dapatkan data konsumsi berdasarkan ID
        $konsumsi = Konsumsi::find($id);

        // Pastikan data ditemukan
        if (!$konsumsi) {
            return redirect()->back()->with('error', 'Data konsumsi tidak ditemukan.');
        }

        // Dapatkan nama user yang sedang login
        $user = Auth::user()->master_user_nama;

        // Proses approval runtut sesuai role user
        switch ($user) {
            case 'asisten_ga':
                if ($konsumsi->status == 0) {
                    $konsumsi->status = 1; // Set status ke 1
                    $konsumsi->save();
                    return redirect()->back()->with('success', 'Pengajuan berhasil di-approve oleh Asisten GA.');
                }
                return redirect()->back()->with('error', 'Asisten GA hanya bisa approve jika status saat ini 0.');

            case 'kasubdiv_ga':
                if ($konsumsi->status == 1) {
                    $konsumsi->status = 2; // Set status ke 2
                    $konsumsi->save();
                    return redirect()->back()->with('success', 'Pengajuan berhasil di-approve oleh Kasubdiv GA.');
                }
                return redirect()->back()->with('error', 'Kasubdiv GA hanya bisa approve jika status saat ini 1.');

            case 'kadiv_ga':
                if ($konsumsi->status == 2) {
                    $konsumsi->status = 3; // Set status ke 3
                    $konsumsi->save();
                    return redirect()->back()->with('success', 'Pengajuan berhasil di-approve oleh Kadiv GA.');
                }
                return redirect()->back()->with('error', 'Kadiv GA hanya bisa approve jika status saat ini 2.');

            default:
                return redirect()->back()->with('error', 'Anda tidak memiliki hak untuk melakukan approval.');
        }
    }

    // Menghapus data konsumsi
    public function destroy(Request $request, $id)
    {
        $konsumsi = Konsumsi::findOrFail($id);

        // Update status menjadi 4 (dibatalkan)
        $konsumsi->status = 4;
        $konsumsi->save();

        return redirect()->route('konsumsi.index')->with('success', 'Permintaan konsumsi dibatalkan.');
    }

    public function kirim(Request $request, $id)
    {
        $ids = explode(',', $id);
        $message = 'Data berhasil dikirim';

        try {
            foreach ($ids as $id) {
                $konsumsi = Konsumsi::where('id', $id)->select('status', 'konsumsi_kirim')->first();

                if ($konsumsi->status == 1) {
                    $konsum = Konsumsi::find($id);
                    $konsum->konsumsi_kirim = 1;
                    $konsum->save();
                    $message = 'Data berhasil dikirim ke Kasubdiv GA';
                } elseif ($konsumsi->status == 2) {
                    $konsum = Konsumsi::find($id);
                    $konsum->konsumsi_kirim = 2;
                    $konsum->save();
                    $message = 'Data berhasil dikirim ke Kadiv GA';
                }
            }

            return redirect()->route('konsumsi.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('konsumsi.index')->with('error', 'Data gagal dikirim.');
        }
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $konsumsiQuery = Konsumsi::with(['sendVicon.bagian']);
            $recordsFiltered = $konsumsiQuery->count();
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);

            // Ambil data berdasarkan pagination
            $konsumsi = $konsumsiQuery->limit($length)->offset($start)->get();

            $data = [];
            foreach ($konsumsi as $index => $item) {
                // Row 1 (Pagi)
                $data[] = [
                    'DT_RowIndex' => ($index + 1),
                    'agenda' => $item->sendvicon->acara ?? 'Tidak ada acara',
                    'tanggal' => Carbon::parse($item->sendvicon->tanggal)->format('d/m/Y') ?? '',
                    'divisi' => $item->sendVicon->bagian->master_bagian_nama ?? '',
                    'makanan' => $item->m_pagi == 1 ? 'Pagi' : 'Tidak ada',
                    'biaya_makanan' => $item->biaya_m_pagi ?? 0,
                    'snack' => $item->s_pagi == 1 ? 'Pagi' : 'Tidak ada',
                    'biaya_snack' => $item->biaya_s_pagi ?? 0,
                    'rowspan' => 3, // Akan digunakan untuk rowspan di JS
                    'keterangan' => $item->keterangan ?? '',
                    'biaya_lain' => $item->biaya_lain ?? 0,
                    'status_approval' => $this->getApprovalStatus($item->status),
                    'action' => '<button class="btn btn-warning btn-sm" onclick="editKonsumsi(' . $item->konsumsi_id . ')">Edit</button>
                             <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $item->konsumsi_id . ')">Delete</button>',
                ];

                // Row 2 (Siang)
                $data[] = [
                    'DT_RowIndex' => ($index + 1),
                    'agenda' => $item->sendvicon->acara,
                    'tanggal' => Carbon::parse($item->sendvicon->tanggal)->format('d/m/Y') ?? '',
                    'divisi' => $item->sendVicon->bagian->master_bagian_nama ?? '',
                    'makanan' => $item->m_siang == 1 ? 'Siang' : 'Tidak ada',
                    'biaya_makanan' => $item->biaya_m_siang ?? 0,
                    'snack' => $item->s_siang == 1 ? 'Siang' : 'Tidak ada',
                    'biaya_snack' => $item->biaya_s_siang ?? 0,
                    'rowspan' => 0, // Tanpa rowspan
                    'keterangan' => $item->keterangan ?? '',
                    'biaya_lain' => $item->biaya_lain ?? 0,
                    'status_approval' => $this->getApprovalStatus($item->status),
                    'action' => '<button class="btn btn-warning btn-sm" onclick="editKonsumsi(' . $item->konsumsi_id . ')">Edit</button>
                             <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $item->konsumsi_id . ')">Delete</button>',
                ];

                // Row 3 (Malam)
                $data[] = [
                    'DT_RowIndex' => ($index + 1),
                    'agenda' => $item->sendvicon->acara,
                    'tanggal' => Carbon::parse($item->sendvicon->tanggal)->format('d/m/Y') ?? '',
                    'divisi' => $item->sendVicon->bagian->master_bagian_nama ?? '',
                    'makanan' => $item->m_malam == 1 ? 'Malam' : 'Tidak ada',
                    'biaya_makanan' => $item->biaya_m_malam ?? 0,
                    'snack' => $item->s_sore == 1 ? 'Sore' : 'Tidak ada',
                    'biaya_snack' => $item->biaya_s_sore ?? 0,
                    'rowspan' => 0,
                    'keterangan' => $item->keterangan ?? '',
                    'biaya_lain' => $item->biaya_lain ?? 0,
                    'status_approval' => $this->getApprovalStatus($item->status),
                    'action' => '<button class="btn btn-warning btn-sm" onclick="editKonsumsi(' . $item->konsumsi_id . ')">Edit</button>
                             <button class="btn btn-danger btn-sm" onclick="confirmDelete(' . $item->konsumsi_id . ')">Delete</button>',
                ];
            }

            return response()->json([
                'draw' => $request->input('draw', 1),
                'data' => $data,
                'recordsTotal' => Konsumsi::count(),
                'recordsFiltered' => $recordsFiltered,
            ]);
        }
        return view('konsumsi.index');
    }

    // Fungsi untuk mengubah status approval
    private function getApprovalStatus($status)
    {
        switch ($status) {
            case 0:
                return 'Waiting for Approve';
            case 1:
                return 'Approved';
            case 2:
                return 'Approve by Kasubdiv GA';
            case 3:
                return 'Approve by Kadiv GA';
            case 4:
                return 'Canceled';
            default:
                return 'Unknown';
        }
    }

    public function exportExcel(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $tanggal_mulai = $request->tanggal_mulai ?  $request->tanggal_mulai : Carbon::parse(SendVicon::with(['konsumsi'])->whereHas('konsumsi')->orderBy('tanggal', 'asc')->first()->tanggal)->format('d-m-Y');
        $tanggal_akhir = $request->tanggal_akhir ?  $request->tanggal_akhir : Carbon::parse(SendVicon::with(['konsumsi'])->whereHas('konsumsi')->orderBy('tanggal', 'desc')->first()->tanggal)->format('d-m-Y');
        $request_status = $request->status != '' ? [$request->status] : [0, 1, 2];
        $status = $request->status ? ($request->status == '0' ? 'Tidak Batal' : ($request->status == '1' ? 'Batal, Sudah Beli' : 'Batal, Belum Beli')) : 'Semua';
        $posisi = $request->posisi ? $request->posisi : 'Semua';
        $divisi = $request->bagian ? (count($request->bagian) ? implode(', ', Bagian::whereIn('master_bagian_id', $request->bagian)->pluck('master_bagian_nama')->toArray()) : 'Semua') : 'Semua';

        try {
            $query = DB::table('konsumsi')
                ->join('sendvicon', 'konsumsi.id_sendvicon', '=', 'sendvicon.id')
                ->join('master_bagian', 'sendvicon.bagian_id', '=', 'master_bagian.master_bagian_id')
                ->where('bagian_regional_id', Auth::user()->bagian->regional->id_regional)
                ->where('konsumsi.status', '!=', 4)
                ->select(
                    'konsumsi.id as konsumsi_id',
                    'konsumsi.keterangan as konsumsi_keterangan',
                    'sendvicon.id as sendvicon_id',
                    'konsumsi.status as konsumsi_status',
                    'sendvicon.keterangan as sendvicon_keterangan',
                    'konsumsi.*',
                    'sendvicon.*',
                    'master_bagian.*'
                );

            if ($request->tanggal_mulai) {
                $query->where('sendvicon.tanggal', '>=', implode('-', array_reverse(explode('-', $request->tanggal_mulai))));
            }

            if ($request->tanggal_akhir) {
                $query->where('sendvicon.tanggal', '<=', implode('-', array_reverse(explode('-', $request->tanggal_akhir))));
            }

            if ($request->bagian) {
                $query->whereIn('master_bagian.master_bagian_id', $request->bagian);
            }

            if ($request->posisi != '') {
                if ($request->posisi == 'kadiv') {
                    $query->whereIn('konsumsi.status', [2, 3]);
                }

                if ($request->posisi == 'kasubdiv') {
                    $query->where('konsumsi.konsumsi_kirim', 1)
                        ->whereIn('konsumsi.status', [1, 2]);
                }

                if ($request->posisi == 'asisten') {
                    $query->where('konsumsi.konsumsi_kirim', 0)
                        ->whereIn('konsumsi.status', [0, 1]);
                }
            }

            if ($request->acara != '') {
                $query->where('sendvicon.acara', 'like', '%' . $request->acara . '%');
            }

            $konsumsi = $query->get();

            return Excel::download(new KonsumsiExport($konsumsi, $tanggal_mulai, $tanggal_akhir, $divisi, $posisi, $status, $request_status), 'Laporan_Permintaan_Konsumsi.xlsx');
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 500);
        }
    }
}
