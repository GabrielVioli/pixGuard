<?php

namespace Tests\Unit;

use App\Services\User\PixFormatService;
use Tests\TestCase;

class PixFormatServiceTest extends TestCase
{
    public function test_detects_email_pix_key(): void
    {
        $service = new PixFormatService();

        $this->assertSame('EMAIL', $service->verifyFormatPix('user@example.com'));
    }

    public function test_detects_evp_pix_key(): void
    {
        $service = new PixFormatService();

        $this->assertSame('EVP', $service->verifyFormatPix('123e4567-e89b-12d3-a456-426614174000'));
    }

    public function test_detects_phone_pix_key(): void
    {
        $service = new PixFormatService();

        $this->assertSame('PHONE', $service->verifyFormatPix('(11) 99999-9999'));
    }

    public function test_detects_cpf_pix_key(): void
    {
        $service = new PixFormatService();

        $this->assertSame('CPF', $service->verifyFormatPix('390.533.447-05'));
    }

    public function test_detects_cnpj_pix_key(): void
    {
        $service = new PixFormatService();

        $this->assertSame('CNPJ', $service->verifyFormatPix('04.252.011/0001-10'));
    }

    public function test_returns_invalid_for_unknown_pix_key(): void
    {
        $service = new PixFormatService();

        $this->assertSame('INVALID_OR_UNKNOWN', $service->verifyFormatPix('abc'));
    }
}
