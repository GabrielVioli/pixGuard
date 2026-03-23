<?php

namespace App\Services\Api\BrasilApi;

use Illuminate\Support\Facades\Http;

class CnpjExtrator
{
    public function extract(string $cnpj)
    {
        $response = Http::get("https://brasilapi.com.br/api/cnpj/v1/{$cnpj}");

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }
}
