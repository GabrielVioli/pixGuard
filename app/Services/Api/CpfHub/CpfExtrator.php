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
                'genero'     => 'M',
                'nascimento' => '01/01/2000',
            ];
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey
        ])
            ->timeout(3)
            ->get("https://api.cpfhub.io/cpf/{$cleanCpf}");

        if ($response->failed()) {
            Log::error('Falha no CpfExtrator (CPFHub)', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            return null;
        }

        $data = $response->json();
        $content = $data['data'] ?? [];
        $birthDate = $content['birthDate'] ?? null;

        if (is_string($birthDate) && str_contains($birthDate, '/')) {
            $birthDate = Carbon::createFromFormat('d/m/Y', $birthDate)->format('Y-m-d');
        }

        return [
            'cpf'        => $cleanCpf,
            'nome'       => $content['name'] ?? 'Nome não localizado',
            'situacao'   => $content['situation'] ?? 'Não informada',
            'genero'     => $content['gender'] ?? null,
            'nascimento' => $birthDate,
        ];
    }
}
