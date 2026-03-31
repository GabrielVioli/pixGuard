<?php

namespace Tests\Unit;

use App\Integrations\AbstractApi\EmailClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailClientTest extends TestCase
{
    public function test_analysis_email_returns_json_on_success(): void
    {
        Http::fake([
            'emailreputation.abstractapi.com/v1/*' => Http::response(['email_address' => 'a@b.com'], 200),
        ]);

        $service = new EmailClient();

        $result = $service->analysisEmail('a@b.com');

        $this->assertSame(['email_address' => 'a@b.com'], $result);
    }

    public function test_analysis_email_returns_error_on_failure(): void
    {
        Http::fake([
            'emailreputation.abstractapi.com/v1/*' => Http::response(null, 500),
        ]);

        $service = new EmailClient();

        $result = $service->analysisEmail('a@b.com');

        $this->assertSame(['error' => true, 'status' => 500], $result);
    }
}
