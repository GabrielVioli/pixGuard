<?php

namespace App\Engines\Risk\Rules;

class PhoneRiskRule
{
    public function evaluate(array $data, array $ctx = []): array
    {
        $p = 0;
        $f = [];

        $loc = $data['phone_location'] ?? [];
        $val = $data['phone_validation'] ?? [];
        $risk = $data['phone_risk'] ?? [];
        $carrier = $data['phone_carrier'] ?? [];

        if (($val['is_valid'] ?? true) === false) {
            $f[] = "CRÍTICO: O número de telefone é inválido ou inexistente.";
            return ['points' => 100, 'flags' => $f];
        }

        if (($loc['country_code'] ?? 'BR') !== 'BR') {
            $f[] = "CRÍTICO: DDI Internacional (" . ($loc['country_name'] ?? 'Exterior') . "). Incompatível com perfil de usuário padrão PIX.";
            return ['points' => 100, 'flags' => $f];
        }

        if (($risk['is_abuse_detected'] ?? false) === true) {
            $f[] = "CRÍTICO: Número com histórico detectado de abuso/spam na rede de telecomunicações.";
            return ['points' => 100, 'flags' => $f];
        }

        if (($risk['is_disposable'] ?? false) === true) {
            $f[] = "CRÍTICO: Uso de número descartável (Burner phone). Vetor clássico de fraude.";
            return ['points' => 100, 'flags' => $f];
        }

        if (($val['is_voip'] ?? false) === true || strtolower($carrier['line_type'] ?? '') === 'voip') {
            $p += 60;
            $f[] = "ALERTA: Linha identificada como VoIP. Alto risco de mascaramento de identidade.";
        }

        $riskLevel = strtolower($risk['risk_level'] ?? 'low');
        if ($riskLevel === 'high') {
            $p += 50;
            $f[] = "ALERTA: Nível de risco geral do número é considerado ALTO pela operadora.";
        } elseif ($riskLevel === 'medium') {
            $p += 20;
            $f[] = "Atenção: Nível de risco geral do número é considerado MODERADO.";
        }

        return [
            'points' => min($p, 100),
            'flags' => $f
        ];
    }
}
