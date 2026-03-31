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

        $this->assertSame(80, $result['points']);
        $this->assertStringContainsString('status', $result['flags'][0]);
    }

    public function test_flags_gender_mismatch(): void
    {
        $rule = new CpfRiskRule();

        $result = $rule->evaluate(['genero' => 'F'], ['genero_detectado' => 'M']);

        $this->assertSame(50, $result['points']);
        $this->assertStringContainsString('Divergência', $result['flags'][0]);
    }

    public function test_flags_age_outlier_for_commercial_context(): void
    {
        $rule = new CpfRiskRule();

        $result = $rule->evaluate(
            ['nascimento' => '1940-01-01'],
            ['categoria_golpe' => 'Produto/Serviço']
        );

        $this->assertSame(25, $result['points']);
        $this->assertStringContainsString('Heurística', $result['flags'][0]);
    }
}
