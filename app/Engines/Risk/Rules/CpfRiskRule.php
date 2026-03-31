<?php

namespace App\Engines\Risk\Rules;

use Carbon\Carbon;

class CpfRiskRule
{
    public function evaluate(array $data, array $ctx): array
    {
        $p = 0; $f = [];
        $status = strtoupper($data['situacao'] ?? 'REGULAR');

        if (in_array($status, ['SUSPENSO', 'CANCELADO', 'NULO', 'INATIVO']) || str_contains($status, 'FALECIDO')) {
            $p += 80;
            $f[] = "Identidade: Titular com status '{$status}' na base CPF.";
        }

        $sexoIA = $ctx['genero_detectado'] ?? null;
        $sexoAPI = $data['genero'] ?? null;
        if ($sexoIA && $sexoAPI && strtoupper($sexoIA) !== strtoupper($sexoAPI)) {
            $p += 50;
            $f[] = "Divergência: Perfil da conversa não condiz com o gênero do titular da conta.";
        }

        if (!empty($data['nascimento'])) {
            $idade = Carbon::parse($data['nascimento'])->age;
            $isComercial = ($ctx['categoria_golpe'] ?? '') === 'Produto/Serviço';
            if ($isComercial && ($idade < 19 || $idade > 75)) {
                $p += 25;
                $f[] = "Heurística: Idade do titular ({$idade} anos) incomum para este tipo de cobrança.";
            }
        }

        return ['points' => $p, 'flags' => $f];
    }
}
