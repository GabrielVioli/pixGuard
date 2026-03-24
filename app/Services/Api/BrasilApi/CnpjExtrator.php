<?php

namespace App\Services\Api\BrasilApi;

use Illuminate\Support\Facades\Http;

class CnpjExtrator
{
    public function extract(string $cnpj)
    {

        $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpj);
        $response = Http::get("https://brasilapi.com.br/api/cnpj/v1/{$cnpjLimpo}");

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }
}
