<?php

namespace Tests\Unit;

use App\Services\Api\AbstractApi\PhoneExtrator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PhoneExtratorTest extends TestCase
{
    public function test_analysis_number_returns_json_on_success(): void
    {
        Http::fake([
            'phoneintelligence.abstractapi.com/v1/*' => Http::response(['phone_number' => '+5511999999999'], 200),
        ]);

        $service = new PhoneExtrator();

        $result = $service->analysisNumber('+5511999999999');

        $this->assertSame(['phone_number' => '+5511999999999'], $result);
    }

    public function test_analysis_number_returns_error_on_failure(): void
    {
        Http::fake([
            'phoneintelligence.abstractapi.com/v1/*' => Http::response(null, 503),
        ]);

        $service = new PhoneExtrator();

        $result = $service->analysisNumber('+5511999999999');

        $this->assertSame(['error' => true, 'status' => 503], $result);
    }
}
