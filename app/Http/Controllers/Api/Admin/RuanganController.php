<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    /**
     * Display a listing of the rooms.
     */
    public function index()
    {
        $page = request('page', 1);
        $limit = request('limit', 10);
        $search = request('search', '');

        try {
            $ruangans = Ruangan::where(function ($q) use ($search) {
                if ($search) {
                    $q->where('nama', 'like', "%$search%")
                        ->orWhere('kapasitas', 'like', "%$search%")
                        ->orWhere('lantai', 'like', "%$search%");
                }
            })->where('status', 'Aktif')
                ->paginate($limit, ['*'], 'page', $page);

            if ($ruangans) {
                return ApiResponse::success('Data ditemukan', $ruangans, 200);
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
     * Display the specified room.
     */
    public function show(string $id)
    {
        try {
            $ruangan = Ruangan::where('id', $id)
                ->where('status', 'Aktif')
                ->first();
            if ($ruangan) {
                return ApiResponse::success('Data ditemukan', $ruangan, 200);
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
