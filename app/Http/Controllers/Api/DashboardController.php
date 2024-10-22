<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SendVicon;
use App\Services\ApiResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the dashboard data.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $floor = $request->get('floor', '12');
        try {
            $sendvicons = SendVicon::with(['bagian', 'ruangan', 'jenisrapat', 'masterLink'])
                ->where('tanggal', $date)
                ->whereHas('ruangan', function ($q) use ($floor) {
                    $q->where('status', 'Aktif')->where('lantai', $floor);
                })
                ->get();

            if ($sendvicons->isEmpty()) {
                throw new \Exception('Data not found', 404);
            }

            return ApiResponse::success('Data ditemukan', $sendvicons, 200);
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
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
