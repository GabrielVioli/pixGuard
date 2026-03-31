<?php

namespace App\Engines\Risk\Rules;

class PhoneRiskRule
{
    public function evaluate(array $data): array
    {
        $p = 0; $f = [];
        $loc = $data['phone_location'] ?? [];
        $val = $data['phone_validation'] ?? [];

        if (($loc['country_code'] ?? 'BR') !== 'BR') {
            return ['points' => 100, 'flags' => ["Crítico: DDI Internacional (" . ($loc['country_name'] ?? 'Exterior') . ")."]];
        }

        if (($val['is_voip'] ?? false) === true) {
            $p += 60;
            $f[] = "Rastreabilidade: Linha identificada como VoIP (mascaramento).";
        }

        return ['points' => $p, 'flags' => $f];
    }
}
