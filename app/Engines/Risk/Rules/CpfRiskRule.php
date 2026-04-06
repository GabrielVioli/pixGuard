<?php

namespace App\Engines\Risk\Rules;

use Carbon\Carbon;

class CpfRiskRule
{
    public function evaluate(array $data, array $ctx = []): array
    {
        $p = 0;
        $f = [];

        $status = strtoupper($data['situacao'] ?? 'REGULAR');
        if ($status !== 'REGULAR') {
            if (in_array($status, ['FALECIDO', 'NULO', 'CANCELADO'])) {
                $f[] = "FRAUDE CONFIRMADA: O titular consta como {$status} na base oficial.";
                return ['points' => 100, 'flags' => $f];
            }

            $p += 60;
            $f[] = "ALERTA: CPF em situação irregular (Status: {$status}).";
        }

        if (!empty($data['nascimento'])) {
            try {
                $idade = Carbon::parse($data['nascimento'])->age;
                $valor = (float) ($ctx['amount'] ?? 0);

                $isComercial = ($ctx['ai_result']['categoria_golpe'] ?? '') === 'Produto/Serviço';
                $isUrgencia = ($ctx['ai_result']['classificacao'] ?? '') === 'Urgência/Ameaça';

                if ($idade < 18) {
                    $p += 20;
                    $f[] = "Atenção: Titular da conta é menor de idade ({$idade} anos).";

                    if ($valor > 1000 || $isComercial) {
                        $p += 50;
                        $f[] = "CRÍTICO: Menor de idade recebendo quantia elevada ou atuando em transação comercial.";
                    }
                } elseif ($idade > 70) {
                    $p += 15;
                    $f[] = "Atenção: Titular idoso ({$idade} anos) - Perfil estrutural comum em contas laranjas.";

                    if ($isUrgencia) {
                        $p += 60;
                        $f[] = "CRÍTICO: Conta em nome de idoso recebendo transferência sob contexto de urgência/ameaça.";
                    }
                }
            } catch (\Exception $e) {
            }
        }

        $sexoIA = $ctx['ai_result']['genero_detectado'] ?? null;
        $sexoAPI = $data['genero'] ?? null;

        if ($sexoIA && $sexoAPI && strtoupper(substr($sexoIA, 0, 1)) !== strtoupper(substr($sexoAPI, 0, 1))) {
            $p += 70;
            $f[] = "CRÍTICO: Incompatibilidade de gênero. O locutor da conversa não corresponde ao titular do CPF.";
        }

        return [
            'points' => min($p, 100),
            'flags' => $f
        ];
    }
}
