<?php

namespace Tests\Unit\Engines\Risk\Rules;

use App\Engines\Risk\Rules\CnpjRiskRule;
use Tests\TestCase;

class CnpjRiskRuleTest extends TestCase
{
    public function test_flags_inactive_cnpj(): void
    {
        $rule = new CnpjRiskRule();

        $result = $rule->evaluate(['situacao' => 'BAIXADA'], []);

        $this->assertSame(80, $result['points']);
        $this->assertStringContainsString('CNPJ inativo', $result['flags'][0]);
    }

    public function test_allows_active_cnpj(): void
    {
        $rule = new CnpjRiskRule();

        $result = $rule->evaluate(['situacao' => 'ATIVA'], []);

        $this->assertSame(0, $result['points']);
        $this->assertSame([], $result['flags']);
    }
}
