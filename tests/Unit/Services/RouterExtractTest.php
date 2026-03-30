<?php

namespace Tests\Unit\Services;

use App\Services\Api\AbstractApi\EmailExtrator;
use App\Services\Api\AbstractApi\PhoneExtrator;
use App\Services\Api\BrasilApi\CnpjExtrator;
use App\Services\Api\CpfHub\CpfExtrator;
use App\Services\ScoreEngine\RouterExtract;
use App\Services\User\PixFormatService;
use Mockery;
use Tests\TestCase;

class RouterExtractTest extends TestCase
{
    public function test_routes_email_extraction(): void
    {
        $pixFormat = Mockery::mock(PixFormatService::class);
        $pixFormat->shouldReceive('verifyFormatPix')->once()->andReturn('EMAIL');

        $emailExtrator = Mockery::mock(EmailExtrator::class);
        $emailExtrator->shouldReceive('analysisEmail')
            ->once()
            ->with('user@example.com')
            ->andReturn(['email' => 'user@example.com']);

        $cpfExtrator = Mockery::mock(CpfExtrator::class);
        $phoneExtrator = Mockery::mock(PhoneExtrator::class);
        $cnpjExtrator = Mockery::mock(CnpjExtrator::class);

        $service = new RouterExtract(
            $pixFormat,
            $emailExtrator,
            $cpfExtrator,
            $phoneExtrator,
            $cnpjExtrator
        );

        $result = $service->RouterExtract(['pix_key' => 'user@example.com']);

        $this->assertSame('EMAIL', $result['key_type']);
        $this->assertSame(['email' => 'user@example.com'], $result['raw_data']);
    }

    public function test_routes_phone_extraction(): void
    {
        $pixFormat = Mockery::mock(PixFormatService::class);
        $pixFormat->shouldReceive('verifyFormatPix')->once()->andReturn('PHONE');

        $phoneExtrator = Mockery::mock(PhoneExtrator::class);
        $phoneExtrator->shouldReceive('analysisNumber')
            ->once()
            ->with('(11) 99999-9999')
            ->andReturn(['phone' => 'ok']);

        $emailExtrator = Mockery::mock(EmailExtrator::class);
        $cpfExtrator = Mockery::mock(CpfExtrator::class);
        $cnpjExtrator = Mockery::mock(CnpjExtrator::class);

        $service = new RouterExtract(
            $pixFormat,
            $emailExtrator,
            $cpfExtrator,
            $phoneExtrator,
            $cnpjExtrator
        );

        $result = $service->RouterExtract(['pix_key' => '(11) 99999-9999']);

        $this->assertSame('PHONE', $result['key_type']);
        $this->assertSame(['phone' => 'ok'], $result['raw_data']);
    }

    public function test_throws_on_unknown_pix_key_type(): void
    {
        $pixFormat = Mockery::mock(PixFormatService::class);
        $pixFormat->shouldReceive('verifyFormatPix')->once()->andReturn('INVALID_OR_UNKNOWN');

        $service = new RouterExtract(
            $pixFormat,
            Mockery::mock(EmailExtrator::class),
            Mockery::mock(CpfExtrator::class),
            Mockery::mock(PhoneExtrator::class),
            Mockery::mock(CnpjExtrator::class)
        );

        $this->expectException(\InvalidArgumentException::class);

        $service->RouterExtract(['pix_key' => 'qualquer']);
    }
}
