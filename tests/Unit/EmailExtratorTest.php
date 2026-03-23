<?php

namespace Tests\Unit;

use App\Services\Api\AbstractApi\EmailExtrator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class EmailExtratorTest extends TestCase
{
    public function test_analysis_email_returns_json_on_success(): void
    {
        Http::fake([
            'emailreputation.abstractapi.com/v1/*' => Http::response(['email_address' => 'a@b.com'], 200),
        ]);

        $service = new EmailExtrator();

        $result = $service->analysisEmail('a@b.com');

        $this->assertSame(['email_address' => 'a@b.com'], $result);
    }

    public function test_analysis_email_returns_error_on_failure(): void
    {
        Http::fake([
            'emailreputation.abstractapi.com/v1/*' => Http::response(null, 500),
        ]);

        $service = new EmailExtrator();

        $result = $service->analysisEmail('a@b.com');

        $this->assertSame(['error' => true, 'status' => 500], $result);
    }
}
