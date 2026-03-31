<?php

namespace App\Services\ScoreEngine;

class EvaluateCnpjRulesService
{
    protected function evaluateCnpjRules(array $data, array $ctx): array
    {
        $p = 0; $f = [];
        if (($data['situacao'] ?? 'ATIVA') !== 'ATIVA') {
            $p += 80;
            $f[] = "Identidade: CNPJ inativo ou baixado.";
        }
        return ['points' => $p, 'flags' => $f];
    }
}
