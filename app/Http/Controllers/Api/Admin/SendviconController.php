<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\FunctionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSendviconAdminRequest;
use App\Http\Resources\SendviconCustomResource;
use App\Http\Resources\SendviconResource;
use App\Models\Bagian;
use App\Models\JenisRapat;
use App\Models\Konsumsi;
use App\Models\Ruangan;
use App\Models\SendVicon;
use App\Services\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SendviconController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $sendvicons = SendVicon::with(['bagian', 'ruangan', 'jenisrapat', 'feedback'])
                ->orderByDesc('tanggal')
                ->orderBy('waktu')
                ->limit(10)
                ->get();

            if ($sendvicons->isEmpty()) {
                throw new \Exception('Data not found', 404);
            }

            $results = $sendvicons->map(function ($sendvicon) {
                $status_nama = SendVicon::cekvicon_nama_waktu($sendvicon->acara, $sendvicon->tanggal, $sendvicon->waktu, $sendvicon->waktu2) > 1 ? 1 : 0;
                $status_ruangan = SendVicon::cek_vicon_ruangan_waktu($sendvicon->id_ruangan, $sendvicon->tanggal, $sendvicon->waktu, $sendvicon->waktu2) > 1 ? 1 : 0;

                return [
                    'items' => $sendvicon,
                    'status_nama' => $status_nama,
                    'status_ruangan' => $status_ruangan
                ];
            });

            return ApiResponse::success('Data ditemukan', $results, 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 500);
        }
    }

    /**
     * Store a newly created vicon in storage.
     */
    public function store(StoreSendviconAdminRequest $request)
    {
        $validated = $request->validated();
        try {
            if ($validated['waktu'] > $validated['waktu2']) {
                throw new \Exception('Waktu akhir harus lebih besar dari waktu Awal', 400);
            }

            $validated['duration'] = FunctionHelper::countDuration($validated['waktu'], $validated['waktu2']);
            $validated['bagian_id'] = Bagian::where('master_bagian_nama', '=', $validated['bagian'])->first()->master_bagian_id;
            $validated['jenisrapat_id'] = JenisRapat::where('nama', '=', $validated['jenisrapat'])->first()->id;

            $validated['id_ruangan'] = null;
            if ($validated['ruangan'] != 'lain') {
                $ruangan = Ruangan::where('nama', '=', $validated['ruangan'])->first();
                if ($ruangan) {
                    $validated['id_ruangan'] = $ruangan->id;
                }
                $validated['ruangan_lain'] = null;
            } else {
                $validated['ruangan'] = null;
            }

            $validated['sk'] = null;
            if ($request->hasFile('sk')) {
                $sk = $request->file('sk');
                $name = $sk->getClientOriginalName();
                $namefile = time() . '_' . $name;
                $sk->move(public_path() . '/uploads/sk', $namefile);
                $validated['sk'] = 'uploads/sk/' . $namefile;
            }

            $validated['status'] = null;
            $dateToday = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            $timeNow = Carbon::now('Asia/Jakarta')->format('H:i');
            if ($validated['tanggal'] == $dateToday) {
                if ($validated['waktu'] > $timeNow) {
                    $validated['status'] = "Booked";
                } else if ($validated['waktu'] < $timeNow) {
                    $validated['status'] = "Expired";
                }
            }

            $validated['token'] = Str::random(8);

            // Create vicon and konsumsi
            $vicon = new SendVicon($validated);
            $vicon->persiapanrapat = '';
            $vicon->persiapanvicon = '';
            $vicon->created = Carbon::now()->format('Y-m-d H:i:s');
            $vicon->is_reminded = 0;
            $vicon->save();
            $vicon->konsumsi()->create([
                'm_pagi' => $validated['m_pagi'] ?? 0,
                'm_siang' => $validated['m_siang'] ?? 0,
                'm_malam' => $validated['m_malam'] ?? 0,
                's_pagi' => $validated['s_pagi'] ?? 0,
                's_siang' => $validated['s_siang'] ?? 0,
                's_sore' => $validated['s_sore'] ?? 0,
            ]);

            // Check ruangan dan jadwal
            $ruanganTersedia = Ruangan::cekviconRuanganWaktu($validated['tanggal'], $validated['waktu'], $validated['waktu2'])->get();

            $arrayRuangan = $ruanganTersedia->pluck('nama')->toArray();

            $statusNama = SendVicon::cekvicon_nama_waktu($validated['acara'], $validated['tanggal'], $validated['waktu'], $validated['waktu2']) > 1 ? 1 : 0;

            $statusRuangan = SendVicon::cek_vicon_ruangan_waktu($validated['id_ruangan'], $validated['tanggal'], $validated['waktu'], $validated['waktu2']) > 1 ? 1 : 0;
            $data = [
                'id' => $vicon->id,
                'items' => new SendviconResource(SendVicon::with('konsumsi')->find($vicon->id)),
                'status_nama' => $statusNama,
                'status_ruangan' => $statusRuangan,
                'available_ruangan' => $arrayRuangan,
            ];
            return (new SendviconCustomResource($data))->response()->setStatusCode(201);
        } catch (\Exception $e) {
            return ApiResponse::error("Data tidak valid", [
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $sendvicon = SendVicon::with(['ruangan', 'bagian', 'jenisrapat', 'feedback'])
                ->where('id', '=', $id)
                ->orderByDesc('tanggal')
                ->orderBy('waktu')
                ->first();

            if (!$sendvicon) {
                throw new \Exception('Data not found', 404);
            }

            $status_nama = SendVicon::cekvicon_nama_waktu($sendvicon->acara, $sendvicon->tanggal, $sendvicon->waktu, $sendvicon->waktu2) > 1 ? 1 : 0;
            $status_ruangan = SendVicon::cek_vicon_ruangan_waktu($sendvicon->id_ruangan, $sendvicon->tanggal, $sendvicon->waktu, $sendvicon->waktu2) > 1 ? 1 : 0;

            return ApiResponse::success('Data ditemukan', [
                'items' => $sendvicon,
                'status_nama' => $status_nama,
                'status_ruangan' => $status_ruangan
            ], 200);
        } catch (\Exception $th) {
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        $array_id = explode(",", $ids);

        if (count($array_id) < 1 || $array_id[0] == '') {
            return response()->json([
                'status' => 'fail',
                'message' => 'data tidak tersedia',
            ], 404);
        }

        // Menggunakan Eloquent untuk menghapus entri berdasarkan ID
        $deletedCount = Sendvicon::destroy($array_id);

        if ($deletedCount > 0) {
            return ApiResponse::success('Data Berhasil dicancel', [], 201);
        } else {
            return ApiResponse::error('Data gagal di hapus', [], 500);
        }
    }

    /**
     * Display a listing of the resource with pagination.
     */
    public function pagination(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $cari = $request->input('cari', '');
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');
        $agendadireksi = $request->input('agendadireksi');
        $vicon = $request->input('vicon');
        $jenisrapat = $request->input('jenisrapat');
        $bagian = $request->input('bagian');
        $acara = $request->input('acara', '');

        try {
            $query = SendVicon::with(['ruangan', 'feedback', 'bagian', 'jenisrapat'])
                ->when($cari, function ($q) use ($cari) {
                    $q->where('acara', 'like', '%' . str_replace("'", '', $cari) . '%');
                })
                ->when($tgl_awal, fn($q) => $q->where('tanggal', '>=', $tgl_awal))
                ->when($tgl_akhir, fn($q) => $q->where('tanggal', '<=', $tgl_akhir))
                ->when($acara, function ($q) use ($acara) {
                    $q->where('acara', 'like', '%' . str_replace("'", '', $acara) . '%');
                })
                ->when($agendadireksi, fn($q) => $q->where('agenda_direksi', $agendadireksi))
                ->when($vicon, fn($q) => $q->where('vicon', $vicon))
                ->when($bagian, fn($q) => $q->whereHas(
                    'bagian',
                    fn($q2) =>
                    $q2->where('bagian', 'like', "%$bagian%")
                ))
                ->when($jenisrapat, fn($q) => $q->whereHas(
                    'jenisrapat',
                    fn($q2) =>
                    $q2->where('nama', 'like', "%$jenisrapat%")
                ))
                ->orderBy('tanggal', 'desc')
                ->orderBy('waktu');

            $sendvicons = $query->paginate($limit, ['*'], 'page', $page);

            // return ApiResponse::success('Data ditemukan', $sendvicons, 200);
            $results = $sendvicons->map(function ($sendvicon) {
                $statusNama = SendVicon::cekvicon_nama_waktu($sendvicon->acara, $sendvicon->tanggal, $sendvicon->waktu, $sendvicon->waktu2) > 1 ? 1 : 0;
                $statusRuangan = SendVicon::cek_vicon_ruangan_waktu($sendvicon->id_ruangan, $sendvicon->tanggal, $sendvicon->waktu, $sendvicon->waktu2) > 1 ? 1 : 0;

                return [
                    'vicon' => $sendvicon,
                    'status_nama' => $statusNama,
                    'status_ruangan' => $statusRuangan
                ];
            });

            return ApiResponse::success('Data ditemukan', [
                'current_page' => $sendvicons->currentPage(),
                'items' => $results,
                'first_page_url' => $sendvicons->url(1),
                'from' => $sendvicons->firstItem(),
                'last_page' => $sendvicons->lastPage(),
                'last_page_url' => $sendvicons->url($sendvicons->lastPage()),
                'links' => $sendvicons->links(),
                'next_page_url' => $sendvicons->nextPageUrl(),
                'path' => $sendvicons->path(),
                'per_page' => $sendvicons->perPage(),
                'prev_page_url' => $sendvicons->previousPageUrl(),
                'to' => $sendvicons->lastItem(),
                'total' => $sendvicons->total(),
            ], 200);
        } catch (\Throwable $th) {
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        }
    }

    /**
     * Get pagination data for today
     */
    public function paginationToday(Request $request)
    {
        $page = $request->input('page', 1); // Default page 1
        $limit = $request->input('limit', 10); // Default limit 10
        $cari = $request->input('cari', '');
        $cari_v = str_replace("'", "", $cari);

        $today = now()->toDateString();

        try {
            $sendvicons = Sendvicon::with(['ruangan', 'feedback', 'bagian', 'jenisrapat'])
                ->where('tanggal', $today)
                ->when($cari, fn($q) => $q->where('acara', 'like', "%$cari_v%"))
                ->orderBy('waktu')
                ->paginate($limit, ['*'], 'page', $page);

            if ($sendvicons->isEmpty()) {
                throw new \Exception('Data Tidak Ditemukan', 404);
            }

            $results = $sendvicons->map(function ($sendvicon) {
                $statusNama = SendVicon::cekvicon_nama_waktu($sendvicon->acara, $sendvicon->tanggal, $sendvicon->waktu, $sendvicon->waktu2) > 1 ? 1 : 0;
                $statusRuangan = SendVicon::cek_vicon_ruangan_waktu($sendvicon->id_ruangan, $sendvicon->tanggal, $sendvicon->waktu, $sendvicon->waktu2) > 1 ? 1 : 0;

                return [
                    'vicon' => $sendvicon,
                    'status_nama' => $statusNama,
                    'status_ruangan' => $statusRuangan
                ];
            });

            return ApiResponse::success('Data ditemukan', [
                'current_page' => $sendvicons->currentPage(),
                'items' => $results,
                'first_page_url' => $sendvicons->url(1),
                'from' => $sendvicons->firstItem(),
                'last_page' => $sendvicons->lastPage(),
                'last_page_url' => $sendvicons->url($sendvicons->lastPage()),
                'links' => $sendvicons->links(),
                'next_page_url' => $sendvicons->nextPageUrl(),
                'path' => $sendvicons->path(),
                'per_page' => $sendvicons->perPage(),
                'prev_page_url' => $sendvicons->previousPageUrl(),
                'to' => $sendvicons->lastItem(),
                'total' => $sendvicons->total(),
            ], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Get pagination data for report
     */
    public function paginationReport(Request $request)
    {
        $page = $request->input('page', 1); // Default page 1
        $limit = $request->input('limit', 10); // Default limit 10

        try {
            $query = Sendvicon::select('tanggal')
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'DESC');

            $sendvicons = $query->paginate($limit, ['*'], 'page', $page);

            if ($sendvicons->isEmpty()) {
                return ApiResponse::error('Data Tidak Ditemukan', [], 404);
            }

            $results = $sendvicons->map(function ($sendvicon) {
                return [
                    'tanggal' => $sendvicon->tanggal,
                    'jumlah_agenda' => Sendvicon::where('tanggal', $sendvicon->tanggal)->count()
                ];
            });

            return ApiResponse::success('Data ditemukan', [
                'current_page' => $sendvicons->currentPage(),
                'items' => $results,
                'first_page_url' => $sendvicons->url(1),
                'from' => $sendvicons->firstItem(),
                'last_page' => $sendvicons->lastPage(),
                'last_page_url' => $sendvicons->url($sendvicons->lastPage()),
                'links' => $sendvicons->links(),
                'next_page_url' => $sendvicons->nextPageUrl(),
                'path' => $sendvicons->path(),
                'per_page' => $sendvicons->perPage(),
                'prev_page_url' => $sendvicons->previousPageUrl(),
                'to' => $sendvicons->lastItem(),
                'total' => $sendvicons->total(),
            ], 200);
        } catch (\Throwable $th) {
            return ApiResponse::error($th->getMessage(), [], 500);
        }
    }

    /**
     * Get list notifikasi
     */
    public function notification(Request $request)
    {
        $username = $request->input('username');
        $petugas = $request->input('petugas');

        // Ambil notifikasi berdasarkan petugas
        if ($username === null) {
            return ApiResponse::error('Username Harus Diisi', [], 400);
        }

        $list_notifikasi = collect();

        if ($petugas === 'TI') {
            $list_notifikasi = SendVicon::getListNotifikasiSendvicon($username, 'petugasti', 'persiapanvicon');
        } elseif ($petugas === 'Umum') {
            $list_notifikasi = SendVicon::getListNotifikasiSendvicon($username, 'petugasruangrapat', 'persiapanrapat');
        } else {
            return response()->json(['message' => 'Invalid petugas type'], 400);
        }
    }

    /**
     * Export data vicon with filter
     */
    public function export(Request $request)
    {
        $tgl_awal = $request->input('tgl_awal');
        $tgl_akhir = $request->input('tgl_akhir');
        $agendadireksi = $request->input('agendadireksi');
        $vicon = $request->input('vicon');
        $jenisrapat = $request->input('jenisrapat');
        $bagian = $request->input('bagian');
        $acara = $request->input('acara');

        try {
            $sendvicons = Sendvicon::with(['ruangan', 'bagian', 'jenisrapat'])
                ->when($tgl_awal, fn($q) => $q->where('tanggal', '>=', $tgl_awal))
                ->when($tgl_akhir, fn($q) => $q->where('tanggal', '<=', $tgl_akhir))
                ->when($acara, fn($q) => $q->where('acara', 'like', "%$acara%"))
                ->when($agendadireksi, fn($q) => $q->where('agenda_direksi', $agendadireksi))
                ->when($vicon, fn($q) => $q->where('vicon', $vicon))
                ->when($jenisrapat, fn($q) => $q->whereHas(
                    'jenisrapat',
                    fn($q2) => $q2->where('nama', 'like', "%$jenisrapat%")
                ))
                ->when($bagian, fn($q) => $q->whereHas(
                    'bagian',
                    fn($q2) =>
                    $q2->where('bagian', 'like', "%$bagian%")
                ))
                ->orderBy('tanggal', 'asc')
                ->orderBy('waktu')
                ->get();

            if ($sendvicons->isEmpty()) {
                throw new \Exception('Data tidak ditemukan', 404);
            }

            return ApiResponse::success('Data ditemukan', $sendvicons, 200);
        } catch (\Throwable $th) {
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        }
    }

    /**
     * Get data presensi by id vicon
     */
    public function presensis($id)
    {
        try {
            $sendvicons = SendVicon::with(['ruangan', 'absensis', 'bagian', 'jenisrapat'])
                ->where('id', '=', $id)
                ->first();

            if (!$sendvicons) {
                throw new \Exception('Data Tidak Ditemukan', 404);
            }

            return ApiResponse::success('Data ditemukan', $sendvicons, 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Update status presensi
     */
    public function presensiStatus(Request $request, $id)
    {
        $validated = Validator::make($request->all(), [
            'status_absensi' => 'required',
        ]);

        if ($validated->fails()) {
            return ApiResponse::error('validation error', $validated->errors(), 422);
        }

        $vicon = SendVicon::find($id);
        if (!$vicon) {
            return ApiResponse::error('Data Tidak Ditemukan', [], 404);
        }

        $vicon->status_absensi = $request->input('status_absensi');
        $vicon->save();

        return ApiResponse::success('Data status presensi berhasil diupdate', $vicon, 200);
    }

    /**
     * Approve vicon
     *
     * @param int $id
     */
    public function approve(int $id)
    {
        $user = request()->current_user;

        if (!in_array($user->master_hak_akses_id, [2, 4])) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Anda tidak punya hak akses untuk melakukan persetujuan vicon'
                    ],
                ]
            ], 403));
        }

        $vicon = SendVicon::find($id);

        if (!$vicon) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Data Vicon Tidak Ditemukan'
                    ],
                ]
            ], 404));
        }

        if ($vicon->status_approval == 1) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Data Vicon Sudah Diapprove'
                    ],
                ]
            ], 403));
        }

        $konsumsi = Konsumsi::where('id_sendvicon', $id)->first();

        if (!$konsumsi) {
            throw new HttpResponseException(response([
                'errors' => [
                    'message' => [
                        'Data Konsumsi Tidak Ditemukan'
                    ],
                ]
            ], 404));
        }

        if ($konsumsi->status == 0 || $konsumsi->status == '' || $konsumsi->status == null) {
            $konsumsi->status = 1;
            $konsumsi->save();
        }

        $vicon->status_approval = 1;
        $vicon->save();

        return ApiResponse::success('Vicon Berhasil Diapprove', SendVicon::with('konsumsi')->find($vicon->id), 200);
    }
}
