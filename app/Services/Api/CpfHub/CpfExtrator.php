<?php

namespace App\Services\Api\CpfHub;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CpfExtrator
{
    private string $apiKey;

    public function __construct() {
        $this->apiKey = config('services.cpfhub.key');
    }

    public function extract(string $cpf): ?array {
        $cleanCpf = preg_replace('/[^0-9]/', '', $cpf);

        if ($cleanCpf === '00000000000') {
            return [
                'cpf'        => $cleanCpf,
                'nome'       => 'USUARIO TESTE (SEM CUSTO)',
                'situacao'   => 'REGULAR',
                'nascimento' => '2000-01-01',
            ];
        }

        try {
            $response = Http::withHeaders(['x-api-key' => $this->apiKey])
                ->timeout(5)
                ->get("https://api.cpfhub.io/cpf/{$cleanCpf}");

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();
            $content = $data['data'] ?? $data;
            $birthDate = $content['birthDate'] ?? null;

            if ($birthDate && str_contains($birthDate, '/')) {
                $birthDate = Carbon::createFromFormat('d/m/Y', $birthDate)->format('Y-m-d');
            }

            return [
                'cpf'        => $cleanCpf,
                'nome'       => $content['name'] ?? 'NOME NAO LOCALIZADO',
                'situacao'   => strtoupper($content['situation'] ?? 'REGULAR'),
                'nascimento' => $birthDate,
            ];

        } catch (\Exception $e) {
            Log::error("Erro fatal no CpfExtrator: " . $e->getMessage());
            return null;
        }
    }
}
