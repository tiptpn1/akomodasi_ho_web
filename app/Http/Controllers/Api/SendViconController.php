<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SendVicon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SendViconController extends Controller
{
    public function index(Request $request)
    {
        $data = SendVicon::with(['ruangan', 'bagian', 'jenisrapat'])->where('privat', 'Tidak')->limit(50)->get();
        return response()->json([
            'message' => 'Success get data vicon',
            'data' => $data
        ], 200);
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $data = SendVicon::with(['ruangan', 'bagian', 'jenisrapat'])->where('privat', 'Tidak')->latest('tanggal')->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('formatted_time', function ($row) {
                    // Format waktu menjadi "HH.mm"
                    $startTime = date('H.i', strtotime($row->waktu)); // Mengambil waktu dari 'waktu'
                    $endTime = date('H.i', strtotime($row->waktu2)); // Mengambil waktu dari 'waktu2'
                    return "$startTime - $endTime"; // Menggabungkan menjadi "HH.mm - HH.mm"
                })
                ->addColumn('action', function ($row) {
                    $btn = "<button style=\"margin-right: 6px; margin-bottom: 3px;\" class=\"btn btn-primary btn-sm\" onclick=\"detail({$row->id})\">Detail</button>";
                    return $btn;
                })->rawColumns(['action'])->make(true);
        }
    }
}
