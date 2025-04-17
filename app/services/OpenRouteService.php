<?php
    namespace App\Services;

    use Illuminate\Support\Facades\Http;
    
    class OpenRouteService
    {
        public static function getDistanceAndDuration($startLat, $startLng, $endLat, $endLng)
        {
            // $apiKey = env('ORS_API_KEY');
            // // dd($apiKey);
            // $response = Http::withHeaders([
            //     'Authorization' => $apiKey,
            // ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
            //     'coordinates' => [
            //         [$startLng, $startLat], // [lng, lat]
            //         [$endLng, $endLat],
            //     ]
            // ]);
    
            // if ($response->successful()) {
            //     $data = $response->json();
            //     $distance = $data['features'][0]['properties']['segments'][0]['distance'] / 1000; // km
            //     $duration = $data['features'][0]['properties']['segments'][0]['duration'] / 60; // minutes
            //     return [
            //         'distance_km' => round($distance, 2),
            //         'duration_min' => round($duration, 2),
            //     ];
            // }
    
            // return null;
            $apiKey = config('services.openrouteservice.key');

        $response = Http::withHeaders([
            'Authorization' => $apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.openrouteservice.org/v2/directions/driving-car/json', [
            'coordinates' => [
                [$startLng, $startLat],
                [$endLng, $endLat],
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();

            // Ambil dari routes -> 0 -> summary
            if (isset($data['routes'][0]['summary'])) {
                $summary = $data['routes'][0]['summary'];
                return [
                    'distance_km' => round($summary['distance'] / 1000, 2),
                    'duration_min' => round($summary['duration'] / 60, 2),
                ];
            } else {
                return ['error' => 'Unexpected response structure', 'data' => $data];
            }
        }

        return [
            'error' => 'ORS API failed',
            'status' => $response->status(),
            'body' => $response->body(),
        ];
        }
    }
    