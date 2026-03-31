<?php

namespace Tests\Unit\Engines\Risk\Rules;

use App\Engines\Risk\Rules\EvpRiskRule;
use Tests\TestCase;

class EvpRiskRuleTest extends TestCase
{
    public function test_returns_fixed_score_and_flag(): void
    {
        $rule = new EvpRiskRule();

        $result = $rule->evaluate([]);

        $this->assertSame(25, $result['points']);
        $this->assertCount(1, $result['flags']);
        $this->assertStringContainsString('EVP', $result['flags'][0]);
    }
}
