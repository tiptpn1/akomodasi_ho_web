<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisRapat;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class JenisRapatController extends Controller
{
    /**
     * Display a listing of the kind of meetings.
     */
    public function index()
    {
        $page = request('page', 1);
        $limit = request('limit', 10);
        $search = request('search', '');

        try {
            $jenisRapat = JenisRapat::where(function ($q) use ($search) {
                if ($search) {
                    $q->where('nama', 'like', "%$search%");
                }
            })->where('status', 'Aktif')
                ->paginate($limit, ['*'], 'page', $page);

            if ($jenisRapat) {
                return ApiResponse::success('Data ditemukan', $jenisRapat, 200);
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
     * Display the specified meeting.
     */
    public function show(string $id)
    {
        try {
            $bagian = JenisRapat::where('id', $id)
                ->where('status', 'Aktif')
                ->first();
            if ($bagian) {
                return ApiResponse::success('Data ditemukan', $bagian, 200);
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
