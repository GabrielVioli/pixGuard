<?php

namespace App\Services\Api\AbstractApi;
use App\Http\Requests\EmailValidadeRequest;
use http\Env\Response;
use Illuminate\Support\Facades\Http;

class EmailExtrator
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
