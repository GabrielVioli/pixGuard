<?php

namespace Tests\Feature;

use App\Integrations\BrasilApi\CnpjClient;
use Tests\TestCase;

class CnpjControllerTest extends TestCase
{
    public function test_form_cnpj_returns_view(): void
    {
        $response = $this->get('/sandbox/cnpj-form');

        $response->assertOk();
        $response->assertViewIs('Cnpj');
    }

    public function test_get_cnpj_returns_extrator_payload(): void
    {
        $this->mock(CnpjClient::class, function ($mock) {
            $mock->shouldReceive('extract')
                ->once()
                ->with('12.345.678/0001-95')
                ->andReturn(['cnpj' => '12.345.678/0001-95']);
        });

        $response = $this->post('/sandbox/cnpj-send', ['cnpj' => '12.345.678/0001-95']);

        $response->assertOk();
        $response->assertJsonFragment(['cnpj' => '12.345.678/0001-95']);
    }
}
