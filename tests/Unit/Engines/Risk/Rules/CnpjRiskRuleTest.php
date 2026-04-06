<?php

namespace Tests\Unit\Engines\Risk\Rules;

use App\Engines\Risk\Rules\CnpjRiskRule;
use Tests\TestCase;

class CnpjRiskRuleTest extends TestCase
{
    public function test_flags_inactive_cnpj(): void
    {
        $rule = new CnpjRiskRule();

        $result = $rule->evaluate(['descricao_situacao_cadastral' => 'BAIXADA'], []);

        $this->assertSame(80, $result['points']);
        $this->assertStringContainsString('CNPJ inativo', $result['flags'][0]);
    }

    public function test_allows_active_cnpj(): void
    {
        $rule = new CnpjRiskRule();

        $result = $rule->evaluate([
            'descricao_situacao_cadastral' => 'ATIVA',
            'natureza_juridica' => 'Sociedade Limitada',
            'capital_social' => 10000,
            'ddd_telefone_1' => '11',
            'email' => 'contato@empresa.com',
        ], []);

        $this->assertSame(0, $result['points']);
        $this->assertStringContainsString('INFO_CNAE', $result['flags'][0]);
    }
}
