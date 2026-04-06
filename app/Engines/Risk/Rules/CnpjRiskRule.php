<?php

namespace App\Engines\Risk\Rules;

use Carbon\Carbon;

class CnpjRiskRule
{
    public function evaluate(array $data, array $ctx = []): array
    {
        $p = 0;
        $f = [];

        $situacao = $data['descricao_situacao_cadastral'] ?? 'ATIVA';
        if ($situacao !== 'ATIVA') {
            $p += 80;
            $f[] = "CRÍTICO: CNPJ inativo, baixado ou irregular (Status: {$situacao}).";
            return ['points' => $p, 'flags' => $f];
        }

        if (!empty($data['data_inicio_atividade'])) {
            try {
                $abertura = Carbon::parse($data['data_inicio_atividade']);
                $idadeMeses = $abertura->diffInMonths(Carbon::now());

                if ($idadeMeses < 1) {
                    $p += 40;
                    $f[] = "ALERTA: CNPJ recém-criado (menos de 1 mês). Alto risco de ser conta laranja.";
                } elseif ($idadeMeses < 3) {
                    $p += 25;
                    $f[] = "Atenção: CNPJ com menos de 3 meses de operação.";
                } elseif ($idadeMeses < 6) {
                    $p += 10;
                    $f[] = "Atenção: CNPJ relativamente novo (menos de 6 meses).";
                }
            } catch (\Exception $e) {
            }
        }

        $isMei = $data['opcao_pelo_mei'] ?? false;
        $natureza = strtolower($data['natureza_juridica'] ?? '');
        $capital = (float) ($data['capital_social'] ?? 0);
        $isIndividual = $isMei || str_contains($natureza, 'individual') || str_contains($natureza, 'unipessoal');

        if ($isIndividual && $capital <= 1000) {
            $p += 15;
            $f[] = "Perfil: Estrutura individual (MEI/EI) com capital social mínimo.";
        } elseif (!$isIndividual && $capital <= 5000) {
            $p += 30;
            $f[] = "ALERTA: Empresa de médio/grande porte com capital social irrisório ou incompatível.";
        }

        $telefone = trim($data['ddd_telefone_1'] ?? '');
        $email = trim($data['email'] ?? '');
        if (empty($telefone) && empty($email)) {
            $p += 15;
            $f[] = "Atenção: CNPJ sem telefone ou e-mail de contacto registado na base oficial.";
        }

        $cnae = $data['cnae_fiscal_descricao'] ?? 'Não informada';


        $f[] = "INFO_CNAE: " . ($data['cnae_fiscal_descricao'] ?? 'Não informada');
        $f[] = "INFO_RS: " . ($data['razao_social'] ?? 'Não informada');
        $f[] = "INFO_CITY: " . ($data['municipio'] ?? 'N/A') . " - " . ($data['uf'] ?? 'N/A');

        $socios = collect($data['qsa'] ?? [])->pluck('nome_socio')->implode(', ');
        if (!empty($socios)) {
            $f[] = "INFO_PARTNERS: {$socios}";
        }

        return [
            'points' => min($p, 100),
            'flags' => $f
        ];
    }
}
