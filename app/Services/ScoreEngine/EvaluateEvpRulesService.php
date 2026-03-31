<?php

namespace App\Services\ScoreEngine;

class EvaluateEvpRulesService
{
    protected function evaluateEvpRules(array $data): array
    {
        return [
            'points' => 25,
            'flags' => ["Segurança: Chave EVP (Aleatória) dificulta a rastreabilidade P2P."]
        ];
    }
}
