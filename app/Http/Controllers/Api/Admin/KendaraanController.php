<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kendaraan;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    /**
     * Display a listing of the vehicles resource.
     */
    public function index()
    {
        $page = request('page', 1);
        $limit = request('limit', 10);
        $search = request('search', '');

        try {
            $kendaraans = Kendaraan::where(function ($q) use ($search) {
                if ($search) {
                    $q->where('no_polisi', 'like', "%$search%")
                        ->orWhere('keterangan', 'like', "%$search%");
                }
            })->where('status', 'Aktif')
                ->paginate($limit, ['*'], 'page', $page);

            if ($kendaraans) {
                return ApiResponse::success('Data ditemukan', $kendaraans, 200);
            } else {
                throw new \Exception('Data tidak ditemukan', 404);
            }
        } catch (\Exception $e) {
            return ApiResponse::error($e->getMessage(), [], $e->getCode());
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
     * Display the specified vehicle.
     */
    public function show(string $id)
    {
        try {
            $kendaraans = Kendaraan::where('id', $id)
                ->where('status', 'Aktif')
                ->first();
            if ($kendaraans) {
                return ApiResponse::success('Data ditemukan', $kendaraans, 200);
            }
            throw new \Exception('Data Tidak Ditemukan', 404);
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
