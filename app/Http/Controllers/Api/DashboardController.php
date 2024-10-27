<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisRapat;
use App\Models\Ruangan;
use App\Models\SendVicon;
use App\Services\ApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    /**
     * Display a listing of the active SendVicon data for a specific date and floor.
     *
     * This function retrieves data related to SendVicon, including associated 'bagian',
     * 'ruangan', and 'jenisrapat', filtered by the given date and floor. If no data is found,
     * it throws an exception with a 'Data not found' message.
     *
     * @param Request $request The HTTP request object containing 'date' and 'floor' parameters.
     * @return \Illuminate\Http\JsonResponse A successful JSON response with the data or an error response.
     * @throws \Exception If no data is found for the given date and floor.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $floor = $request->get('floor', '12');
        try {
            $sendvicons = SendVicon::with(['bagian', 'ruangan', 'jenisrapat'])
                ->where('tanggal', $date)
                ->whereHas('ruangan', function ($q) use ($floor) {
                    $q->where('status', 'Aktif')->where('lantai', $floor);
                })
                ->get();

            if ($sendvicons->isEmpty()) {
                throw new \Exception('Data not found', 404);
            }

            return ApiResponse::success('Data ditemukan', $sendvicons, 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Export the dashboard agenda data for a given floor and date to a PDF file.
     *
     * This function retrieves data related to meeting rooms and meetings scheduled
     * on a specific floor and date, then generates a PDF report of the agenda.
     * The PDF is formatted in landscape orientation on A4 paper.
     *
     * @param Request $request The HTTP request object containing 'lantai' and 'date' parameters.
     * @return \Illuminate\Http\Response A PDF download response or a JSON error response.
     */
    public function export(Request $request)
    {
        /**
         * The floor number to retrieve data for.
         * @example 14
         * @default 12
         */
        $floor = $request->input('lantai', '12');
        /**
         * The date to retrieve data for.
         * @example 2024-12-12
         * @default 2024-12-12 (now)
         */
        $date = $request->input('date', date('Y-m-d'));
        $jenis_rapat = JenisRapat::all();

        try {
            $ruangan = Ruangan::with(['sendVicons', 'sendVicons.jenisrapat', 'sendVicons.bagian'])
                ->where('lantai', $floor)
                ->get();

            $data = [
                'ruangan' => $ruangan,
                'date' => $date,
                'lantai' => $floor,
                'jenis_rapat' => $jenis_rapat,
                'carbon' => new Carbon(),
            ];

            $pdf = Pdf::loadView('admin.dashboardagenda.export_pdf', $data)
                ->setPaper('A4', 'landscape');

            // Return the PDF as a download response with the desired filename
            return $pdf->download('Laporan Agenda Lantai ' . $floor . ' pada Tanggal ' . $date  . '_' . time() . '.pdf');
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
