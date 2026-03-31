<?php

namespace Tests\Unit\Engines\Risk\Rules;

use App\Engines\Risk\Rules\PhoneRiskRule;
use Tests\TestCase;

class PhoneRiskRuleTest extends TestCase
{
    public function test_blocks_international_numbers(): void
    {
        $rule = new PhoneRiskRule();

        $result = $rule->evaluate([
            'phone_location' => [
                'country_code' => 'US',
                'country_name' => 'Estados Unidos',
            ],
        ]);

        $this->assertSame(100, $result['points']);
        $this->assertStringContainsString('Internacional', $result['flags'][0]);
    }

    public function test_flags_voip_numbers(): void
    {
        $rule = new PhoneRiskRule();

        $result = $rule->evaluate([
            'phone_location' => [
                'country_code' => 'BR',
            ],
            'phone_validation' => [
                'is_voip' => true,
            ],
        ]);

        $this->assertSame(60, $result['points']);
        $this->assertStringContainsString('VoIP', $result['flags'][0]);
    }

    public function test_allows_valid_domestic_number(): void
    {
        $rule = new PhoneRiskRule();

        $result = $rule->evaluate([
            'phone_location' => [
                'country_code' => 'BR',
            ],
            'phone_validation' => [
                'is_voip' => false,
            ],
        ]);

        $this->assertSame(0, $result['points']);
        $this->assertSame([], $result['flags']);
    }
}
