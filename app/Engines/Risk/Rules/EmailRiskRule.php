<?php

namespace App\Engines\Risk\Rules;

class EmailRiskRule
{
    public function evaluate(array $data, array $ctx = []): array
    {
        $p = 0;
        $f = [];

        $qual = $data['email_quality'] ?? [];
        $dom = $data['email_domain'] ?? [];
        $deliv = $data['email_deliverability'] ?? [];

        if (($qual['is_disposable'] ?? false) === true) {
            $f[] = "CRÍTICO: Uso de provedor de e-mail temporário/descartável (Alta correlação com fraude).";
            return ['points' => 100, 'flags' => $f];
        }

        if (($deliv['status'] ?? 'deliverable') !== 'deliverable') {
            $p += 80;
            $f[] = "ALERTA: O e-mail informado não existe ou não pode receber mensagens (Status: {$deliv['status']}).";
        }


        $score = $qual['score'] ?? 1.0;
        if ($score <= 0.2) {
            $p += 60;
            $f[] = "ALERTA: Qualidade geral do e-mail é considerada péssima pela rede (Score: {$score}).";
        } elseif ($score <= 0.5) {
            $p += 30;
            $f[] = "Atenção: Histórico e qualidade do e-mail são suspeitos (Score: {$score}).";
        }

        if (($dom['is_risky_tld'] ?? false) === true) {
            $p += 40;
            $f[] = "Atenção: O domínio utiliza uma extensão (TLD) comumente associada a spam e golpes.";
        }


        if (($qual['is_username_suspicious'] ?? false) === true) {
            $p += 10;
            $f[] = "Perfil: Nome de usuário apresenta padrão alfanumérico gerado automaticamente.";
        }

        return [
            'points' => min($p, 100),
            'flags' => $f
        ];
    }
}
