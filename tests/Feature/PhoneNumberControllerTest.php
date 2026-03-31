<?php

namespace Tests\Feature;

use App\Integrations\AbstractApi\PhoneClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhoneNumberControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_number_returns_view(): void
    {
        $response = $this->get('/sandbox/form-phone');

        $response->assertOk();
        $response->assertViewIs('formNumberPhone');
    }

    public function test_get_phone_creates_record_and_redirects_back(): void
    {
        $this->mock(PhoneClient::class, function ($mock) {
            $mock->shouldReceive('analysisNumber')
                ->once()
                ->andReturn([
                    'phone_number' => '+5511999999999',
                    'phone_location' => ['region' => 'SP'],
                    'phone_carrier' => ['line_type' => 'mobile'],
                    'phone_validation' => [
                        'is_voip' => false,
                        'is_valid' => true,
                        'line_status' => 'active',
                    ],
                    'phone_risk' => [
                        'risk_level' => 'low',
                        'is_disposable' => false,
                        'is_abuse_detected' => false,
                    ],
                ]);
        });

        $response = $this->from('/sandbox/form-phone')->post('/sandbox/phone', [
            'phone' => '(11) 99999-9999',
        ]);

        $response->assertRedirect('/sandbox/form-phone');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('phone_analyses', [
            'phone_number' => '+5511999999999',
            'region' => 'SP',
            'line_type' => 'mobile',
            'risk_level' => 'low',
        ]);
    }
}
