<?php

namespace App\Engines\Risk;

use App\Engines\Risk\Rules\CnpjRiskRule;
use App\Engines\Risk\Rules\CpfRiskRule;
use App\Engines\Risk\Rules\EmailRiskRule;
use App\Engines\Risk\Rules\PhoneRiskRule;
use App\Engines\Risk\Rules\EvpRiskRule;

class RiskEvaluator
{
    private const WEIGHTS = [
        'ai_context' => 0.40,
        'bureau'     => 0.60,
    ];

    public function __construct(
        protected EvpRiskRule $evpRiskRule,
        protected PhoneRiskRule $phoneRiskRule,
        protected EmailRiskRule $emailRiskRule,
        protected CpfRiskRule $cpfRiskRule,
        protected CnpjRiskRule $cnpjRiskRule
    ) {}

    public function analyze(array $data, array $aiContext): array
    {
        $keyType = $data['key_type'] ?? 'EVP';
        $rawData = $data['raw_data'] ?? null;
        $amount  = (float) ($data['amount'] ?? 0);
        $allFlags = [];
        $isCritical = false;
        $bureauScore = 0;

        $aiScore = (int) ($aiContext['score_contexto'] ?? 0);
        if ($aiScore > 0) {
            $allFlags[] = "IA: " . ($aiContext['motivo'] ?? 'Risco comportamental detectado.');
        }

        if (!is_null($rawData)) {
            $ctx = ['amount' => $amount, 'ai_result' => $aiContext];
            $res = match($keyType) {
                'CPF'   => $this->cpfRiskRule->evaluate($rawData, $ctx),
                'CNPJ'  => $this->cnpjRiskRule->evaluate($rawData, $ctx),
                'PHONE' => $this->phoneRiskRule->evaluate($rawData, $ctx),
                'EMAIL' => $this->emailRiskRule->evaluate($rawData, $ctx),
                'EVP'   => $this->evpRiskRule->evaluate($rawData, $ctx),
                default => ['points' => 0, 'flags' => []]
            };

            $bureauScore = $res['points'];
            $allFlags = array_merge($allFlags, $res['flags']);
        } else {
            $allFlags[] = "Atenção: Consulta de dados oficiais (Receita/Bureau) indisponível no momento.";
            $bureauScore = 0;
        }

        foreach ($allFlags as $flag) {
            if (str_contains(strtoupper($flag), 'CRÍTICO') || str_contains(strtoupper($flag), 'FRAUDE')) {
                $isCritical = true;
                break;
            }
        }

        $finalScore = $isCritical ? 100 : ($aiScore * 0.4) + ($bureauScore * 0.6);
        $finalScore = (int) round($finalScore);

        return [
            'veredict' => [
                'final_score'    => $finalScore,
                'risk_level'     => $this->getClassificacao($finalScore),
                'risk_color'     => $finalScore > 60 ? 'red' : ($finalScore > 30 ? 'amber' : 'green'),
                'recommendation' => $this->getRecommendation($finalScore),
            ],
            'evidences' => [
                'flags' => $allFlags,
            ],
            'behavioral' => [
                'ocr_text_raw'      => $aiContext['texto_original'] ?? '',
                'ai_reasoning'      => $aiContext['motivo'] ?? 'Análise comportamental concluída.',
                'detected_triggers' => $aiContext['gatilhos_encontrados'] ?? [],
                'ai_intent'         => $aiContext['classificacao'] ?? 'Comercial',
            ],
            'audit' => [
                'amount'          => $amount,
                'pix_key_type'    => $keyType,
                'analysis_at'     => now()->format('d/m/Y H:i'),
                'api_status'      => is_null($rawData) ? ['status' => 'partial_fail'] : ['status' => 'success'],
                'proof_url'       => $data['proof_path'] ?? null,
            ],
            'metadata' => $rawData ?? []
        ];
    }

    private function getClassificacao(int $score): string
    {
        if ($score >= 60) return 'Alto Risco';
        if ($score >= 30) return 'Atenção';
        return 'Seguro';
    }

    private function getRecommendation(int $score): string
    {
        if ($score >= 60) {
            return "OPERAÇÃO DE ALTO RISCO. Não realize este pagamento. Os dados indicam alta probabilidade de golpe.";
        }
        if ($score >= 30) {
            return "ATENÇÃO. Verifique os dados com cautela antes de prosseguir com a transferência.";
        }
        return "TRANSAÇÃO SEGURA. Os dados do favorecido foram validados com sucesso.";
    }
}
