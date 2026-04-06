<?php

namespace Tests\Unit\Engines\Risk\Rules;

use App\Engines\Risk\Rules\CpfRiskRule;
use Tests\TestCase;

class CpfRiskRuleTest extends TestCase
{
    public function test_flags_invalid_status(): void
    {
        $rule = new CpfRiskRule();

        $result = $rule->evaluate(['situacao' => 'SUSPENSO'], []);

        $this->assertSame(60, $result['points']);
        $this->assertStringContainsString('irregular', $result['flags'][0]);
    }

    public function test_flags_gender_mismatch(): void
    {
        $rule = new CpfRiskRule();

        $result = $rule->evaluate(['genero' => 'F'], ['ai_result' => ['genero_detectado' => 'M']]);

        $this->assertSame(70, $result['points']);
        $this->assertStringContainsString('Incompatibilidade', $result['flags'][0]);
    }

    public function test_flags_elderly_risk(): void
    {
        $rule = new CpfRiskRule();

        $result = $rule->evaluate(['nascimento' => '1940-01-01'], []);

        $this->assertSame(15, $result['points']);
        $this->assertStringContainsString('idoso', $result['flags'][0]);
    }
}
