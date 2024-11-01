<?php

namespace App\Http\Controllers;

use App\Models\JenisRapat;
use App\Models\Ruangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardAgendaController extends Controller
{
    var $model;

    public function __construct()
    {
        $this->model = new Ruangan();
    }

    public function index()
    {
        $list_lantai = $this->model->getDataDistinct('lantai');

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
            $ruangan = $this->model->getSpesificData(array('lantai' => $lantai), $date);

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
            $all_lantai = $this->model->getDataDistinct('lantai')->pluck('lantai');

            foreach($all_lantai as $lantai) {
                $all_ruangan[] = $this->model->getSpesificData(array('lantai' => $lantai), $date);
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
