<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bagian;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class BagianController extends Controller
{
    /**
     * Display a listing of the Bagian resource.
     */
    public function index()
    {
        $page = request('page', 1);
        $limit = request('limit', 10);
        $search = request('search', '');

        try {
            $bagians = Bagian::where(function ($q) use ($search) {
                if ($search) {
                    $q->where('master_bagian_nama', 'like', "%$search%")
                        ->orWhere('master_bagian_posisi', 'like', "%$search%")
                        ->orWhere('master_bagian_kode', 'like', "%$search%");
                }
            })->where('is_active', 1)
                ->paginate($limit, ['*'], 'page', $page);

            if ($bagians) {
                return ApiResponse::success('Data ditemukan', $bagians, 200);
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
     * Display the specified bagian.
     */
    public function show(string $id)
    {
        try {
            $bagian = Bagian::where('master_bagian_id', $id)
                ->where('is_active', 1)
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
