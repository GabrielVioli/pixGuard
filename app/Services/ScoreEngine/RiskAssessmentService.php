<?php

namespace App\Services\ScoreEngine;

use Illuminate\Support\Str;
use Carbon\Carbon;

class RiskAssessmentService
{
    public function analyze(array $data, array $aiContext): array
    {
        $keyType = $data['key_type'];
        $rawData = $data['raw_data'];

        if (is_null($rawData)) {
            return [
                'final_score' => 100,
                'flags' => ['RNF02: Falha crítica na consulta do bureau. Risco máximo aplicado por precaução.'],
                'metadata' => []
            ];
        }

        $totalScore = 0;
        $allFlags = [];

        $aiScore = (int) ($aiContext['score_contexto'] ?? 0);
        $totalScore += $aiScore;
        if ($aiScore > 0) {
            $allFlags[] = "Contexto (IA): " . ($aiContext['motivo'] ?? 'Risco comportamental detectado.');
        }

        $res = match($keyType) {
            'CPF'   => $this->evaluateCpfRules($rawData, $aiContext),
            'CNPJ'  => $this->evaluateCnpjRules($rawData, $aiContext),
            'PHONE' => $this->evaluatePhoneRules($rawData),
            'EMAIL' => $this->evaluateEmailRules($rawData),
            'EVP'   => $this->evaluateEvpRules($rawData),
            default => ['points' => 0, 'flags' => []]
        };

        $totalScore += $res['points'];
        $allFlags = array_merge($allFlags, $res['flags']);

        return [
            'final_score' => min($totalScore, 100),
            'nivel' => $this->getClassificacao(min($totalScore, 100)),
            'flags' => $allFlags,
            'metadata' => $rawData
        ];
    }











    private function getClassificacao(int $score): string
    {
        if ($score <= 29) return 'Seguro';
        if ($score <= 59) return 'Atenção';
        return 'Alto Risco';
    }
}
