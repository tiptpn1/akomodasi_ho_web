<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $token;

    public function __construct()
    {
        $this->token = env('FONNTE_API_KEY');
        // $this->token = config('services.fonnte.token'); // dari .env
        // $this->token = 'DZkjjukDvTTQZv2Ky1Ca';
    }

    public function send($target, $message)
    {
        if (preg_match('/^0/', $target)) {
            $target = preg_replace('/^0/', '62', $target);
        }
        // dd($target,$message,$this->token );
        return Http::withHeaders([  
            'Authorization' => $this->token,
        ])->asForm()->post('https://api.fonnte.com/send', [
            'target' => $target,
            'message' => $message,
            'countryCode' => '62',
        ]);
    }
}
