<?php

namespace App\Engines\Risk\Rules;

class EmailRiskRule
{
    public function evaluate(array $data): array
    {
        $p = 0; $f = [];
        $qual = $data['email_quality'] ?? [];
        $dom = $data['email_domain'] ?? [];

        if (($qual['is_disposable'] ?? false) === true) {
            $p += 50;
            $f[] = "ReputaÃ§Ã£o: Uso de provedor de e-mail temporÃ¡rio.";
        }

        if (($dom['domain_age'] ?? 999) < 180) {
            $p += 50;
            $f[] = "ReputaÃ§Ã£o: DomÃ­nio do e-mail criado hÃ¡ menos de 6 meses.";
        }

        return ['points' => $p, 'flags' => $f];
    }
}
