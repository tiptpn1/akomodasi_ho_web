<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AbsensiExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SendVicon;
use Carbon\Carbon;
use DateInterval;
use DateTime;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiController extends Controller
{
    public function create($token, $id)
    {
        $this->cek_8hours();
        $this->cek_open_presensi();
        $sendvicon = SendVicon::with(['absensis'])->where('id', $id)
            ->where('token', $token)
            ->select(['id', 'acara', 'tanggal', 'waktu', 'waktu2', 'ruangan as nama_ruangan', 'ruangan_lain', 'id_ruangan', 'status_absensi'])
            ->first();

        if ($sendvicon) {
            if ($sendvicon->status_absensi == 'Closed') {
                return view('errors.absensi.link_closed');
            } else if ($sendvicon->status_absensi == 'Open') {
                return view('admin.absensi.form', compact('sendvicon'));
            } else {
                return view('errors.absensi.link_not_opened');
            }
        }

        return view('errors.absensi.link_invalid');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sendvicon,id',
            'nama' => 'required',
            'jabatan' => 'required',
            'instansi' => 'required',
        ]);

        // Ambil sendvicon dengan absensi dan kolom yang sama seperti di create
        $sendvicon = SendVicon::find($request->id);

        $ipInfo = json_decode(file_get_contents("http://ipinfo.io/?token=918a4d948ab18e"));

        $agent = new Agent();
        $sendvicon->absensis()->create([
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'instansi' => $request->instansi,
            'ip' => $request->ip(),
            'city' => $ipInfo->city,
            'region' => $ipInfo->region,
            'country' => $ipInfo->country,
            'loc' => $ipInfo->loc,
            'timezone' => $ipInfo->timezone,
            'browser' => $agent->browser(),
            'os' => $agent->platform(),
        ]);

        return redirect()->route('absensi.create', ['token' => $sendvicon->token, 'id' => $sendvicon->id])->with('success', 'Data absensi berhasil ditambahkan');
    }

    public function show($id)
    {
        $absensi = Absensi::find($id);

        if (!$absensi) {
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
            'message' => 'Success get data absensi',
            'absensi' => $absensi
        ], 200);
    }

    public function exportExcel($id)
    {
        $currentDateTime = Carbon::now()->format('Y-m-d_H-i-s');
        $fileName = "export-file-absensi_$currentDateTime.xlsx";

        return Excel::download(new AbsensiExport($id), $fileName);
    }

    public function status(Request $request, $id)
    {
        $status = $request->get('status_absensi');
        $sendvicon = SendVicon::find($id);

        if ($sendvicon) {
            $sendvicon->status_absensi = $status;
            $sendvicon->save();
        }

        return redirect()->route('admin.absensi.rekap', ['id' => $id]);
    }

    public function rekap($id)
    {
        $this->cek_8hours();
        $this->cek_open_presensi();

        $sendVicon = SendVicon::with(['ruangan', 'absensis'])
            ->find($id);

        return view('admin.absensi.rekap', compact('sendVicon'));
    }

    private function cek_8hours()
    {
        $date = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $hoursToAdd = 8;

        $data = SendVicon::with(['ruangan'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('waktu')
            ->limit(100)
            ->get();

        foreach ($data as $vicon) {
            $tglVicon = Carbon::parse($vicon->tanggal)->format('Y-m-d');
            $endTimeVicon = Carbon::parse($vicon->waktu2)->format('H:i:s');
            $endDatetimeVicon = $tglVicon . " " . $endTimeVicon;

            $startTimeVicon = Carbon::parse($vicon->waktu)->format('H:i:s');
            $startDatetimeVicon = $tglVicon . " " . $startTimeVicon;

            $checkCloseVicon = new DateTime($endDatetimeVicon);
            $checkCloseVicon->add(new DateInterval("PT{$hoursToAdd}H"));
            $closeVicon = $checkCloseVicon->format('Y-m-d H:i:s');

            $now = new DateTime($date);
            $start = new DateTime($startDatetimeVicon);
            $close = new DateTime($closeVicon);

            if ($now > $close) {
                SendVicon::where('id', $vicon->id)->update(['status_absensi' => 'Closed']);
            } else if ($now < $start) {
                SendVicon::where('id', $vicon->id)->update(['status_absensi' => NULL]);
            }
        }
    }

    private function cek_open_presensi()
    {
        $currentDate = Carbon::now('Asia/Jakarta')->format('Y-m-d');
        $currentTime = Carbon::now('Asia/Jakarta')->format('H:i:00');

        $sendVicons = SendVicon::where('status_absensi', '!=', 'Closed')
            ->orWhereNull('status_absensi')
            ->get();


        foreach ($sendVicons as $sendVicon) {
            $date = Carbon::parse($sendVicon->tanggal)->format('Y-m-d');
            $startVicon = Carbon::parse($sendVicon->waktu)->format('H:i:s');
            $endVicon = Carbon::parse($sendVicon->waktu2)->format('H:i:s');

            if ($date == $currentDate && $currentTime >= $startVicon && $currentTime <= $endVicon) {
                SendVicon::where('id', $sendVicon->id)->update(['status_absensi' => 'Open']);
            }
        }
    }
}
