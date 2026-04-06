<?php

namespace App\Integrations\AbstractApi;
use Illuminate\Support\Facades\Http;

class PhoneClient
{
    private string $apiKey;

    public function __construct() {
        $this->apiKey = config('services.abstract.phone_key');
    }

    public function analysisNumber(string $phoneNumber)
    {
        $response = Http::timeout(5)->get("https://phoneintelligence.abstractapi.com/v1/", [
            'api_key' => $this->apiKey,
            'phone'   => $phoneNumber
        ]);

        if($response->failed()) {
            return [
                'error'  => true,
                'status' => $response->status(),
            ];
        }

        return $response->json();
    }
}
