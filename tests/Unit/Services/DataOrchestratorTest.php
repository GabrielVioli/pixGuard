<?php

namespace Tests\Unit\Services;

use App\Integrations\AbstractApi\EmailClient;
use App\Integrations\AbstractApi\PhoneClient;
use App\Integrations\BrasilApi\CnpjClient;
use App\Integrations\CpfHub\CpfClient;
use App\Services\Extraction\DataOrchestrator;
use App\Support\PixFormatValidator;
use Mockery;
use Tests\TestCase;

class DataOrchestratorTest extends TestCase
{
    public function test_routes_email_extraction(): void
    {
        $pixFormat = Mockery::mock(PixFormatValidator::class);
        $pixFormat->shouldReceive('verifyFormatPix')->once()->andReturn('EMAIL');

        $emailClient = Mockery::mock(EmailClient::class);
        $emailClient->shouldReceive('analysisEmail')
            ->once()
            ->with('user@example.com')
            ->andReturn(['email' => 'user@example.com']);

        $cpfClient = Mockery::mock(CpfClient::class);
        $phoneClient = Mockery::mock(PhoneClient::class);
        $cnpjClient = Mockery::mock(CnpjClient::class);

        $service = new DataOrchestrator(
            $pixFormat,
            $emailClient,
            $cpfClient,
            $phoneClient,
            $cnpjClient
        );

        $result = $service->orchestrate(['pix_key' => 'user@example.com']);

        $this->assertSame('EMAIL', $result['key_type']);
        $this->assertSame(['email' => 'user@example.com'], $result['raw_data']);
    }

    public function test_routes_phone_extraction(): void
    {
        $pixFormat = Mockery::mock(PixFormatValidator::class);
        $pixFormat->shouldReceive('verifyFormatPix')->once()->andReturn('PHONE');

        $phoneClient = Mockery::mock(PhoneClient::class);
        $phoneClient->shouldReceive('analysisNumber')
            ->once()
            ->with('(11) 99999-9999')
            ->andReturn(['phone' => 'ok']);

        $emailClient = Mockery::mock(EmailClient::class);
        $cpfClient = Mockery::mock(CpfClient::class);
        $cnpjClient = Mockery::mock(CnpjClient::class);

        $service = new DataOrchestrator(
            $pixFormat,
            $emailClient,
            $cpfClient,
            $phoneClient,
            $cnpjClient
        );

        $result = $service->orchestrate(['pix_key' => '(11) 99999-9999']);

        $this->assertSame('PHONE', $result['key_type']);
        $this->assertSame(['phone' => 'ok'], $result['raw_data']);
    }

    public function test_throws_on_unknown_pix_key_type(): void
    {
        $pixFormat = Mockery::mock(PixFormatValidator::class);
        $pixFormat->shouldReceive('verifyFormatPix')->once()->andReturn('INVALID_OR_UNKNOWN');

        $service = new DataOrchestrator(
            $pixFormat,
            Mockery::mock(EmailClient::class),
            Mockery::mock(CpfClient::class),
            Mockery::mock(PhoneClient::class),
            Mockery::mock(CnpjClient::class)
        );

        $this->expectException(\InvalidArgumentException::class);

        $service->orchestrate(['pix_key' => 'qualquer']);
    }
}
