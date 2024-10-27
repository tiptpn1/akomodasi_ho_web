<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\KonsumsiExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateKonsumsiRequest;
use App\Models\Bagian;
use App\Models\Konsumsi;
use App\Models\SendVicon;
use App\Models\User;
use App\Services\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class KonsumsiController extends Controller
{
    /**
     * Display a listing of the consumption.
     */
    public function index()
    {
        $page = request('page', 1);
        $limit = request('limit', 10);
        try {
            $consumptions = Konsumsi::with(['sendVicon.bagian'])
                ->paginate($limit, ['*'], 'page', $page);

            if ($consumptions) {
                return ApiResponse::success('successfully get data konsumsi', $consumptions, 200);
            }

            throw new \Exception('Data konsumsi tidak ditemukan', 404);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified consumption.
     */
    public function show(string $id)
    {
        try {
            $consumption = Konsumsi::with(['sendVicon.bagian'])->find($id);

            if ($consumption) {
                return ApiResponse::success('successfully get data konsumsi', $consumption, 200);
            }

            throw new \Exception('Data konsumsi tidak ditemukan', 404);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 500);
        }
    }

    /**
     * Update the specified consumption in storage.
     */
    public function update(UpdateKonsumsiRequest $request, string $id)
    {
        $validated = $request->validated();

        try {
            $consumption = Konsumsi::findOrFail($id);

            if ($consumption) {
                $consumption->update($validated);

                return ApiResponse::success('successfully update data konsumsi', $consumption, 200);
            }

            throw new \Exception('Data konsumsi tidak ditemukan', 404);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 500);
        }
    }

    /**
     * Remove the specified consumption.
     */
    public function destroy(string $id)
    {
        try {
            $comsumption = Konsumsi::findOrFail($id);

            if (!$comsumption) {
                throw new \Exception('Data konsumsi tidak ditemukan', 404);
            }

            $comsumption->status = 4;

            $comsumption->save();

            return ApiResponse::success('successfully delete data konsumsi', $comsumption, 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 500);
        }
    }

    /**
     * Approve the specified consumption.
     */
    public function approve(Request $request, $id)
    {
        try {
            $consumption = Konsumsi::findOrFail($id);

            if (!$consumption) {
                throw new \Exception('Data konsumsi tidak ditemukan', 404);
            }

            $user = $request->current_user;

            $username = $user->master_user_nama;
            // Proses approval runtut sesuai role user
            switch ($username) {
                case 'asisten_ga':
                    if ($consumption->status == 0) {
                        $consumption->status = 1; // Set status ke 1
                        $consumption->save();
                        return ApiResponse::success('Pengajuan berhasil di-approve oleh Asisten GA.', $consumption, 200);
                    }
                    throw new \Exception('Asisten GA hanya bisa approve jika status saat ini 0.', 403);

                case 'kasubdiv_ga':
                    if ($consumption->status == 1) {
                        $consumption->status = 2; // Set status ke 2
                        $consumption->save();
                        return ApiResponse::success('Pengajuan berhasil di-approve oleh Kasubdiv GA.', $consumption, 200);
                    }
                    throw new \Exception('Kasubdiv GA hanya bisa approve jika status saat ini 1.', 403);

                case 'kadiv_ga':
                    if ($consumption->status == 2) {
                        $consumption->status = 3; // Set status ke 3
                        $consumption->save();
                        return ApiResponse::success('Pengajuan berhasil di-approve oleh Kadiv GA.', $consumption, 200);
                    }
                    throw new \Exception('Kadiv GA hanya bisa approve jika status saat ini 2.', 403);

                default:
                    throw new \Exception('Anda tidak memiliki akses untuk melakukan approval.', 403);
            }
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        $tanggal_mulai = $request->input('tanggal_mulai', Carbon::parse(SendVicon::with(['konsumsi'])->whereHas('konsumsi')->orderBy('tanggal', 'asc')->first()->tanggal)->format('d-m-Y'));
        $tanggal_akhir = $request->input('tanggal_akhir', Carbon::parse(SendVicon::with(['konsumsi'])->whereHas('konsumsi')->orderBy('tanggal', 'desc')->first()->tanggal)->format('d-m-Y'));
        $status = $request->input('status');
        /**
         * Bagian bisa lebih dari 1, dipisahkan dengan koma(,)
         * @example bagian nama, bagian nama
         */
        $posisi = $request->input('posisi');

        /**
         * Bagian bisa lebih dari 1, dalam bentuk array [1,2,3]
         * @example [1,2,3]
         */
        $bagian = $request->input('bagian');


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
