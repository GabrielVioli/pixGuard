<?php

namespace Tests\Feature;

use App\Services\Api\AbstractApi\EmailExtrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_form_returns_view(): void
    {
        $response = $this->get('/email-form');

        $response->assertOk();
        $response->assertViewIs('email');
    }

    public function test_get_email_creates_record_and_returns_payload(): void
    {
        $this->mock(EmailExtrator::class, function ($mock) {
            $mock->shouldReceive('analysisEmail')
                ->once()
                ->andReturn([
                    'email_address' => 'usuario.teste@example.com',
                    'email_deliverability' => ['status' => 'DELIVERABLE'],
                    'email_quality' => [
                        'score' => 0.95,
                        'is_disposable' => false,
                        'is_free_email' => true,
                    ],
                    'email_risk' => ['address_risk_status' => 'low'],
                    'email_domain' => ['domain_age' => 123],
                    'email_breaches' => ['total_breaches' => 0],
                ]);
        });

        $response = $this->post('/email-send', [
            'email' => 'usuario.teste@example.com',
        ]);

        $response->assertOk();
        $response->assertJsonFragment([
            'email_address' => 'usuario.teste@example.com',
            'deliverability' => 'DELIVERABLE',
            'risk_status' => 'low',
            'domain_age_days' => 123,
            'total_breaches' => 0,
        ]);

        $this->assertDatabaseHas('email_analyses', [
            'email_address' => 'usuario.teste@example.com',
            'deliverability' => 'DELIVERABLE',
        ]);
    }
}
