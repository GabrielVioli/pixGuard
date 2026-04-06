<?php

namespace Tests\Unit\Engines\Risk\Rules;

use App\Engines\Risk\Rules\EmailRiskRule;
use Tests\TestCase;

class EmailRiskRuleTest extends TestCase
{
    public function test_flags_disposable_email(): void
    {
        $rule = new EmailRiskRule();

        $result = $rule->evaluate([
            'email_quality' => ['is_disposable' => true],
            'email_domain' => ['domain_age' => 30],
        ]);

        $this->assertSame(100, $result['points']);
        $this->assertCount(1, $result['flags']);
        $this->assertStringContainsString('tempor', $result['flags'][0]);
    }
}
