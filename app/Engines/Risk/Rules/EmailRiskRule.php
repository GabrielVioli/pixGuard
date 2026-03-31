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
            $f[] = "Reputação: Uso de provedor de e-mail temporário.";
        }

        if (($dom['domain_age'] ?? 999) < 180) {
            $p += 50;
            $f[] = "Reputação: Domínio do e-mail criado há menos de 6 meses.";
        }

        return ['points' => $p, 'flags' => $f];
    }
}
