<?php

namespace App\Engines\Risk;

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Engines\Risk\Rules\CnpjRiskRule;
use App\Engines\Risk\Rules\CpfRiskRule;
use App\Engines\Risk\Rules\EmailRiskRule;
use App\Engines\Risk\Rules\PhoneRiskRule;
use App\Engines\Risk\Rules\EvpRiskRule;

class RiskEvaluator
{

    public function __construct(protected EvpRiskRule $evpRiskRule,
                                protected PhoneRiskRule $phoneRiskRule,
                                protected EmailRiskRule $emailRiskRule,
                                protected CpfRiskRule $cpfRiskRule,
                                protected CnpjRiskRule $cnpjRiskRule
    )
    {}

    public function analyze(array $data, array $aiContext): array
    {
        $keyType = $data['key_type'];
        $rawData = $data['raw_data'];

        if (is_null($rawData)) {
            return [
                'final_score' => 100,
                'flags' => ['RNF02: Falha crÃ­tica na consulta do bureau. Risco mÃ¡ximo aplicado por precauÃ§Ã£o.'],
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
            'CPF'   => $this->cpfRiskRule->evaluate($rawData, $aiContext),
            'CNPJ'  => $this->cnpjRiskRule->evaluate($rawData, $aiContext),
            'PHONE' => $this->phoneRiskRule->evaluate($rawData),
            'EMAIL' => $this->emailRiskRule->evaluate($rawData),
            'EVP'   => $this->evpRiskRule->evaluate($rawData),
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
        if ($score <= 59) return 'AtenÃ§Ã£o';
        return 'Alto Risco';
    }
}
