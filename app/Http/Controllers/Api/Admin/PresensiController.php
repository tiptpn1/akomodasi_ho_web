<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePresensiRequest;
use App\Models\Absensi;
use App\Models\SendVicon;
use App\Services\ApiResponse;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class PresensiController extends Controller
{
    /**
     * Display a listing of the presensi.
     */
    public function index()
    {
        try {
            $absensis = Absensi::with(['sendvicon'])->orderByDesc('created_at')->paginate(10);

            if ($absensis->isEmpty()) {
                throw new \Exception('Data Tidak Ditemukan', 404);
            }
            return ApiResponse::success('Data ditemukan', [
                'current_page' => $absensis->currentPage(),
                'items' => $absensis->items(),
                'first_page_url' => $absensis->url(1),
                'from' => $absensis->firstItem(),
                'last_page' => $absensis->lastPage(),
                'last_page_url' => $absensis->url($absensis->lastPage()),
                'links' => $absensis->links(),
                'next_page_url' => $absensis->nextPageUrl(),
                'path' => $absensis->path(),
                'per_page' => $absensis->perPage(),
                'prev_page_url' => $absensis->previousPageUrl(),
                'to' => $absensis->lastItem(),
                'total' => $absensis->total(),
            ], 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Store a newly created presensi.
     */
    public function store(StorePresensiRequest $request)
    {
        $validated = $request->validated();
        try {
            $sendvicon = SendVicon::find($request->id);

            $ipInfo = json_decode(file_get_contents("http://ipinfo.io/?token=918a4d948ab18e"));

            $agent = new Agent();
            $absensi = $sendvicon->absensis()->create([
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

            return ApiResponse::success('Data absensi berhasil ditambahkan', $absensi, 201);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], $e->getCode());
        }
    }

    /**
     * Display the specified presensi.
     */
    public function show(string $id)
    {
        try {
            $absensi = Absensi::with(['sendvicon'])->find($id);

            if (!$absensi) {
                throw new \Exception('Data Tidak Ditemukan', 404);
            }

            return ApiResponse::success('Data ditemukan', $absensi, 200);
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], $e->getCode());
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
    public function destroy(string $id)
    {
        //
    }
}
