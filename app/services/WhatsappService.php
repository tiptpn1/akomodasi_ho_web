<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $apiKey;
    protected $numberKey;

    public function __construct()
    {
        $this->apiKey = env('WATZAP_API_KEY');
        $this->numberKey = env('WATZAP_NUMBER_KEY');        
    }

    public function send($target, $message)
    {
        if (preg_match('/^0/', $target)) {
            $target = preg_replace('/^0/', '62', $target);
        }

        $data = [
            'api_key' => $this->apiKey,
            'number_key' => $this->numberKey,
            'phone_no' => $target,
            'message' => $message,
            'wait_until_send' => '1'
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post('https://api.watzap.id/v1/send_message', $data);

        return $response; // Atau ->body() jika ingin raw response
    }
}