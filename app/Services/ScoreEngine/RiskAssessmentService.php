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

    private function evaluateCpfRules(array $data, array $ctx): array
    {
        $p = 0; $f = [];
        $status = strtoupper($data['situacao'] ?? 'REGULAR');

        if (in_array($status, ['SUSPENSO', 'CANCELADO', 'NULO', 'INATIVO']) || str_contains($status, 'FALECIDO')) {
            $p += 80;
            $f[] = "Identidade: Titular com status '{$status}' na base CPF.";
        }

        $sexoIA = $ctx['genero_detectado'] ?? null;
        $sexoAPI = $data['genero'] ?? null;
        if ($sexoIA && $sexoAPI && strtoupper($sexoIA) !== strtoupper($sexoAPI)) {
            $p += 50;
            $f[] = "Divergência: Perfil da conversa não condiz com o gênero do titular da conta.";
        }

        if (!empty($data['nascimento'])) {
            $idade = Carbon::parse($data['nascimento'])->age;
            $isComercial = ($ctx['categoria_golpe'] ?? '') === 'Produto/Serviço';
            if ($isComercial && ($idade < 19 || $idade > 75)) {
                $p += 25;
                $f[] = "Heurística: Idade do titular ({$idade} anos) incomum para este tipo de cobrança.";
            }
        }

        return ['points' => $p, 'flags' => $f];
    }

    private function evaluatePhoneRules(array $data): array
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

    private function evaluateEmailRules(array $data): array
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

    private function evaluateEvpRules(array $data): array
    {
        return [
            'points' => 25,
            'flags' => ["Segurança: Chave EVP (Aleatória) dificulta a rastreabilidade P2P."]
        ];
    }

    private function evaluateCnpjRules(array $data, array $ctx): array
    {
        $p = 0; $f = [];
        if (($data['situacao'] ?? 'ATIVA') !== 'ATIVA') {
            $p += 80;
            $f[] = "Identidade: CNPJ inativo ou baixado.";
        }
        return ['points' => $p, 'flags' => $f];
    }

    private function getClassificacao(int $score): string
    {
        if ($score <= 29) return 'Seguro';
        if ($score <= 59) return 'Atenção';
        return 'Alto Risco';
    }
}
