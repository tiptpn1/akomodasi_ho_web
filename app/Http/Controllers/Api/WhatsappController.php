<?php

namespace App\Http\Controllers\Api;

use App\Facades\Whatsapp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\WhatsappService; // pastikan sudah ada service Whatsapp
use App\Jobs\SendWhatsappNotification;


class WhatsappController extends Controller
{
    public function send($number)
    {
        try {
            // Kirim pesan WA
            $message = "test pesan arhan";
            $response = Whatsapp::send($number, $message);

            return response()->json([
                'success' => true,
                'number' => $number,
                'message' => $message,
                'response' => $response
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
