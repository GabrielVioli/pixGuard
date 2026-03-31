<?php

namespace App\Integrations\AbstractApi;

use Illuminate\Support\Facades\Http;

class EmailClient
{
    private string $apiKey;

    public function __construct() {
        $this->apiKey = config('services.abstract.email_key');
    }

    public function analysisEmail(string $email)
    {
        $response = Http::get("https://emailreputation.abstractapi.com/v1/?api_key={$this->apiKey}&email={$email}");

        if($response->failed()) {
            return [
                "error" => true,
                "status" => $response->status()
            ];
        }

        return $response->json();
    }
}
