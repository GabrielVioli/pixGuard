<?php

namespace App\Services\Api\AbstractApi;
use Illuminate\Support\Facades\Http;

class PhoneExtrator
{
    private string $phoneNumber;
    private string $apiKey;

    public function __construct() {
        $this->apiKey = config('services.abstract.phone_key');
    }

    public function extract(){
        $response = Http::withOptions([
            'verify' => false,
        ])->get("https://phoneintelligence.abstractapi.com/v1/?api_key={$this->apiKey}&phone={$this->phoneNumber}");

        if($response->failed()) {
            return [
                'error' => true,
                'status' => $response->status(),
            ];
        }

        return $response->json();

    }
    public function analysisNumber(string $phoneNumber){
        $this->phoneNumber = $phoneNumber;

        return $this->extract();
    }

}
