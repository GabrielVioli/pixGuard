<?php

namespace App\Engines\Risk\Rules;

class EvpRiskRule
{
    public function evaluate(array $data): array
    {
        return [
            'points' => 25,
            'flags' => ["Segurança: Chave EVP (Aleatória) dificulta a rastreabilidade P2P."]
        ];
    }
}
