<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Dashboard\ExportByDate;
use App\Http\Controllers\Controller;
use App\Models\SendVicon;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function index()
    {
        $countVicon = SendVicon::select('tanggal')
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->paginate(8);

        return view('admin.dasboard.index', compact('countVicon'));
    }

    public function show($id)
    {
        $data = SendVicon::with(['ruangan', 'bagian', 'jenisrapat', 'masterLink'])->find($id);
        if (!$data) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "data not found"
                    ]
                ]
            ], 404));
        }

        return response()->json([
            'success' => true,
            'message' => 'Success get data vicon',
            'data' => $data
        ], 200);
    }


    public function getAjaxDashboard(Request $request)
    {
        // Auto-update status
        $this->autoUpdate();

        // Fetch data from model
        $list = $this->getDatatablesDashboard($request);

        $data = [];
        $no = $request->get('start') ?? 0;

        $daftarHari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        // dd($list);

        foreach ($list as $item) {
            $no++;
            $settgl = Carbon::parse($item->tanggal)->format('d-m-Y');
            $setwaktu = Carbon::parse($item->waktu)->format('H:i');
            $setwaktu2 = Carbon::parse($item->waktu2)->format('H:i');
            $namahari = Carbon::parse($item->tanggal)->format('l');

            $cek = '0,';
            $cekAcara = '0,';

            $cekviconNamaWaktu = $this->cekviconNamaWaktu($item->acara, $item->tanggal, $item->waktu, $item->waktu2);
            $cekJadwal = $this->cekViconRuanganWaktu($item->id_ruangan, $item->tanggal, $item->waktu, $item->waktu2);

            if ($cekJadwal > 1) {
                $cek = "1,";
            }
            if ($cekviconNamaWaktu > 1) {
                $cekAcara = "1,";
            }

            $row = [];
            $row[] = "$no.";
            $row[] = $cekAcara . $item->acara;
            $row[] = $daftarHari[$namahari] . ", " . $settgl;
            $row[] = $setwaktu . " - " . $setwaktu2;
            $row[] = $cek . ($item->id_ruangan != null ? $item->ruangan : $item->ruangan_lain);
            $row[] = $item->bagian->bagian;
            $row[] = $item->vicon == 'Ya' ? $item->vicon . "\n" . $item->jenis_link : $item->vicon;
            $row[] = $item->keterangan;
            $row[] = Carbon::parse($item->created_at)->format('Y-m-d H:i:s');

            $actionButtons = '<center>
                                <button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" onclick="detail(' . $item->id . ')" class="btn btn-primary btn-sm"><div class="d-flex align-items-center justify-content-center"><i class="fas fa-eye"></i></div>';
            if (!is_null($item->token)) {
                $actionButtons .= '<button style="margin-right: 6px; margin-bottom: 3px; width:30px; height:30px;" onclick="absensi(' . $item->id . ')" class="btn btn-outline-success btn-sm"><div class="d-flex align-items-center justify-content-center"><i class="fas fa-star"></i></div>';
            }
            $row[] = $actionButtons;

            $data[] = $row;
        }

        return response()->json([
            'draw' => $request->get('draw'),
            'recordsTotal' => SendVicon::count(),
            'recordsFiltered' => $this->countFilteredDashboard($request),
            'data' => $data,
        ]);
    }

    private function autoUpdate()
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now()->toTimeString();

        // SendVicon::where('status', '!=', 'Cancel')
        //     ->whereNull('petugasti')
        //     ->orWhere('petugasti', '')
        //     ->update(['status' => 'Booked']);

        SendVicon::where('status', '!=', 'Cancel')
            // ->whereNotNull('petugasti')
            ->where('tanggal', '>=', $today)
            ->update(['status' => 'Confirm']);

        SendVicon::where('status', '!=', 'Cancel')
            ->where('tanggal', '<=', $today)
            ->where('waktu', '<=', $now)
            ->whereIn('status', ['Confirm', 'Booked'])
            ->update(['status' => 'Expired']);
    }

    private function getDatatablesDashboard(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $query = SendVicon::with('ruangan')
            ->whereDate('tanggal', $today)
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu', 'asc');

        // Search functionality
        if ($searchValue = $request->input('search.value')) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('acara', 'like', "%$searchValue%")
                    ->orWhere('tanggal', 'like', "%$searchValue%")
                    ->orWhere('waktu', 'like', "%$searchValue%")
                    ->orWhereHas('ruangan', function ($q) use ($searchValue) {
                        $q->where('nama', 'like', "%$searchValue%");
                    })
                    ->orWhere('ruangan_lain', 'like', "%$searchValue%")
                    ->orWhere('bagian', 'like', "%$searchValue%")
                    ->orWhere('vicon', 'like', "%$searchValue%");
            });
        }

        $currentPage = ($request->get('start') / $request->get('length')) + 1;

        return $query->paginate(10, ['*'], 'page', $currentPage);
    }

    private function cekviconNamaWaktu($acara, $tanggal, $waktu1, $waktu2)
    {
        $acara_v = str_replace("'", "", $acara);
        return SendVicon::where('acara', $acara_v)
            ->where('tanggal', $tanggal)
            ->where('waktu', '<=', $waktu2)
            ->where('waktu2', '>=', $waktu1)
            ->count();
    }

    private function cekViconRuanganWaktu($ruangan, $tanggal, $waktu1, $waktu2)
    {
        if (!is_null($ruangan) && $ruangan != 5) {
            return SendVicon::where('id_ruangan', $ruangan)
                ->where('tanggal', $tanggal)
                ->where('waktu', '<=', $waktu2)
                ->where('waktu2', '>=', $waktu1)
                ->count();
        }
        return 0;
    }

    private function countFilteredDashboard(Request $request)
    {
        return $this->getDatatablesDashboard($request)->total();
    }

    public function exportPdfVicon(Request $request)
    {
        $startDate = $request->input('tanggal_awal', Carbon::today()->toDateString());
        $endDate = $request->input('tanggal_akhir', Carbon::today()->toDateString());

        $start = Carbon::parse($startDate)->format('Y-m-d');
        $end = Carbon::parse($endDate)->format('Y-m-d');

        $downloadTime = Carbon::now()->format('Y-m-d H-i-s');

        $sendvicon = SendVicon::with(['bagian'])
            ->where('tanggal', $start)
            ->where('tanggal', $end)
            ->get();

        $view_data = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'download_time' => $downloadTime,
            'sendvicon' => $sendvicon
        ];

        $pdf = Pdf::loadView('exports.pdf.export_by_date', $view_data)->setPaper('A4', 'landscape');
        return $pdf->stream('Rekap Agenda ' . $downloadTime . '.pdf');
    }

    public function exportExcelVicon(Request $request)
    {
        $startDate = $request->input('tanggal_awal', Carbon::today()->toDateString());
        $endDate = $request->input('tanggal_akhir', Carbon::today()->toDateString());

        $start = Carbon::parse($startDate)->format('Y-m-d');
        $end = Carbon::parse($endDate)->format('Y-m-d');

        $downloadTime = Carbon::now()->format('Y-m-d H-i-s');

        $sendvicon = SendVicon::where('tanggal', $start)
            ->where('tanggal', $end)
            ->get();

        return Excel::download(
            new ExportByDate($startDate, $endDate, $downloadTime, $sendvicon),
            'Rekap Agenda ' . $downloadTime . '.xlsx'
        );
    }
}
