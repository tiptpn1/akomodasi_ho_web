<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateKonsumsiRequest;
use App\Models\Konsumsi;
use App\Models\User;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

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
}
