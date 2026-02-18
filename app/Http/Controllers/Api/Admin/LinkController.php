<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterLink;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    /**
     * Display a listing of the links.
     */
    public function index()
    {
        $page = request('page', 1);
        $limit = request('limit', 10);
        $search = request('search', '');

        try {
            $links = MasterLink::where(function ($q) use ($search) {
                if ($search) {
                    $q->where('namalink', 'like', "%$search%");
                }
            })->where('status', 'Aktif')
                ->paginate($limit, ['*'], 'page', $page);

            if ($links) {
                return ApiResponse::success('Data ditemukan', $links, 200);
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
     * Display the specified link.
     */
    public function show(string $id)
    {
        try {
            $link = MasterLink::where('id', $id)
                ->where('status', 'Aktif')
                ->first();
            if ($link) {
                return ApiResponse::success('Data ditemukan', $link, 200);
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
