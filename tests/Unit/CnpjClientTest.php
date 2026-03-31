<?php

namespace Tests\Unit;

use App\Integrations\BrasilApi\CnpjClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CnpjClientTest extends TestCase
{
    public function test_extract_returns_json_on_success(): void
    {
        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/*' => Http::response(['cnpj' => '12345678000195'], 200),
        ]);

        $service = new CnpjClient();

        $result = $service->extract('12345678000195');

        $this->assertSame(['cnpj' => '12345678000195'], $result);
    }

    public function test_extract_returns_null_on_failure(): void
    {
        Http::fake([
            'brasilapi.com.br/api/cnpj/v1/*' => Http::response(null, 404),
        ]);

        $service = new CnpjClient();

        $result = $service->extract('00000000000000');

        $this->assertNull($result);
    }
}
