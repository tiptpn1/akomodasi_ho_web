<?php

namespace App\Http\Controllers;

use App\Models\JenisRapat;
use App\Models\Bagian;
use App\Models\Ruangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardAgendaController extends Controller
{
    
    var $model;

    public function __construct()
    {
        $this->model = new Ruangan();
    }


    public function index()
    {
        // Ambil bagian_reg berdasarkan master_bagian_id dari user yang login
        $bagian_reg = Bagian::where('master_bagian_id', Auth::user()->master_nama_bagian_id)
            ->orderBy('master_bagian_id', 'desc')
            ->first();
    
        // Pastikan $bagian_reg tidak null sebelum lanjut
        if (!$bagian_reg) {
            return back()->with('error', 'Data bagian regional tidak ditemukan.');
        }
    
        // Ambil daftar lantai berdasarkan ruangan_regional_id
        $list_lantai = Ruangan::select('lantai')
            ->where('ruangan_regional_id', $bagian_reg->bagian_regional_id)
            ->where('lantai','!=',0)
            ->distinct()
            ->get(); // Gunakan get() agar hasilnya koleksi model, bukan array
    
        $data = [
            'list_lantai' => $list_lantai,
        ];
    
        return view('admin.dashboardagenda.index', $data);
    }
    

    public function getContent(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Your session has ended'], 419);
        }

        $lantai = $request->lt;
        $split_tanggal = explode('/', $request->date);
        $date = implode('-', [$split_tanggal[2],$split_tanggal[0],$split_tanggal[1]]);
        $jenis_rapat = JenisRapat::all();

        try {
            // $ruangan = $this->model->getSpesificData(array('lantai' => $lantai), $date);

            $ruangan = $this->model->getSpesificData(array('lantai' => $lantai), $date)
                    ->where('ruangan_regional_id', Auth::user()->bagian->regional->id_regional)
                    ->get(); // Tambahkan ->get() untuk mengeksekusi query

            $data = [
                'ruangan' => $ruangan,
                'jenis_rapat' => $jenis_rapat,
                'carbon' => new Carbon(),
            ];

            return view('admin.dashboardagenda.table_agenda', $data);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 400);
        }
    }

    public function export_pdf(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Your session has ended'], 419);
        }

        $split_tanggal = explode('/', $request->date);
        $date = implode('-', [$split_tanggal[2],$split_tanggal[0],$split_tanggal[1]]);
        $jenis_rapat = JenisRapat::all();

        try {
            $all_ruangan = [];
            $userRegionalId = Auth::user()->bagian->regional->id_regional;
            // $all_lantai = $this->model->getDataDistinct('lantai')->pluck('lantai');
            $all_lantai = $this->model->getDataDistinct('lantai', null, $userRegionalId)
                         ->pluck('lantai'); // Sekarang Anda bisa memanggil pluck di sini

            foreach($all_lantai as $lantai) {
                // $all_ruangan[] = $this->model->getSpesificData(array('lantai' => $lantai), $date);

                $all_ruangan[] = $this->model->getSpesificData(array('lantai' => $lantai), $date)
                    ->where('ruangan_regional_id', Auth::user()->bagian->regional->id_regional)
                    ->get(); // Tambahkan ->get() untuk mengeksekusi query
            }

            $data = [
                'all_ruangan' => $all_ruangan,
                'date' => $date,
                'all_lantai' => $all_lantai,
                'jenis_rapat' => $jenis_rapat,
                'carbon' => new Carbon(),
            ];

            $pdf = Pdf::loadView('admin.dashboardagenda.export_pdf', $data)
                    ->setPaper('A4', 'landscape');

            return $pdf->stream('Laporan Agenda pada Tanggal ' . $date  . '_' . time() . '.pdf');
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'line' => $th->getLine(),
                'file' => $th->getFile(),
            ], 400);
        }
    }
}
