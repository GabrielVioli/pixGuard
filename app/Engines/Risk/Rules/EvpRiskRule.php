<?php

namespace App\Engines\Risk\Rules;

class EvpRiskRule
{
    public function evaluate(array $data): array
    {
        return [
            'points' => 25,
            'flags' => ["SeguranÃ§a: Chave EVP (AleatÃ³ria) dificulta a rastreabilidade P2P."]
        ];
    }
}
