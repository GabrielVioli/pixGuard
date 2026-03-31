<?php

namespace Tests\Unit\Services;

use App\Engines\Risk\RiskEvaluator;
use App\Engines\Risk\Rules\CnpjRiskRule;
use App\Engines\Risk\Rules\CpfRiskRule;
use App\Engines\Risk\Rules\EmailRiskRule;
use App\Engines\Risk\Rules\EvpRiskRule;
use App\Engines\Risk\Rules\PhoneRiskRule;
use Tests\TestCase;

class RiskEvaluatorTest extends TestCase
{
    public function test_analyze_flags_voip_phone(): void
    {
        $service = new RiskEvaluator(
            new EvpRiskRule(),
            new PhoneRiskRule(),
            new EmailRiskRule(),
            new CpfRiskRule(),
            new CnpjRiskRule()
        );

        $result = $service->analyze([
            'key_type' => 'PHONE',
            'raw_data' => [
                'phone_validation' => [
                    'is_voip' => true,
                ],
            ],
        ], []);

        $this->assertSame(60, $result['final_score']);
        $this->assertSame('Alto Risco', $result['nivel']);
        $this->assertStringContainsString('VoIP', $result['flags'][0]);
    }

    public function test_analyze_allows_clean_phone(): void
    {
        $service = new RiskEvaluator(
            new EvpRiskRule(),
            new PhoneRiskRule(),
            new EmailRiskRule(),
            new CpfRiskRule(),
            new CnpjRiskRule()
        );

        $result = $service->analyze([
            'key_type' => 'PHONE',
            'raw_data' => [
                'phone_validation' => [
                    'is_voip' => false,
                ],
                'phone_location' => [
                    'country_code' => 'BR',
                ],
            ],
        ], []);

        $this->assertSame(0, $result['final_score']);
        $this->assertSame('Seguro', $result['nivel']);
        $this->assertSame([], $result['flags']);
    }

    public function test_analyze_returns_hard_block_for_international_phone(): void
    {
        $service = new RiskEvaluator(
            new EvpRiskRule(),
            new PhoneRiskRule(),
            new EmailRiskRule(),
            new CpfRiskRule(),
            new CnpjRiskRule()
        );

        $result = $service->analyze([
            'key_type' => 'PHONE',
            'raw_data' => [
                'phone_location' => [
                    'country_code' => 'US',
                    'country_name' => 'Estados Unidos',
                ],
            ],
        ], []);

        $this->assertSame(100, $result['final_score']);
        $this->assertSame('Alto Risco', $result['nivel']);
        $this->assertStringContainsString('Internacional', $result['flags'][0]);
    }

    public function test_analyze_scores_email_with_ai_context(): void
    {
        $service = new RiskEvaluator(
            new EvpRiskRule(),
            new PhoneRiskRule(),
            new EmailRiskRule(),
            new CpfRiskRule(),
            new CnpjRiskRule()
        );

        $result = $service->analyze([
            'key_type' => 'EMAIL',
            'raw_data' => [
                'email_quality' => ['is_disposable' => true],
                'email_domain' => ['domain_age' => 30],
            ],
        ], [
            'score_contexto' => 40,
            'motivo' => 'Urgencia suspeita',
        ]);

        $this->assertSame(100, $result['final_score']);
        $this->assertSame('Alto Risco', $result['nivel']);
        $this->assertStringContainsString('Contexto (IA)', $result['flags'][0]);
        $this->assertGreaterThanOrEqual(3, count($result['flags']));
    }

    public function test_analyze_returns_rnf02_when_bureau_fails(): void
    {
        $service = new RiskEvaluator(
            new EvpRiskRule(),
            new PhoneRiskRule(),
            new EmailRiskRule(),
            new CpfRiskRule(),
            new CnpjRiskRule()
        );

        $result = $service->analyze([
            'key_type' => 'CPF',
            'raw_data' => null,
        ], []);

        $this->assertSame(100, $result['final_score']);
        $this->assertStringStartsWith('RNF02:', $result['flags'][0]);
        $this->assertSame([], $result['metadata']);
    }
}
