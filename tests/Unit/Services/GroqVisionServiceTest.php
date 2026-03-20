<?php

namespace Tests\Unit\Services;

use App\Services\GroqApi\GroqVisionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GroqVisionServiceTest extends TestCase
{
    public function test_analyze_screenshot_returns_decoded_json(): void
    {
        config(['services.groq.key' => 'test-key']);

        Storage::fake('public');
        Storage::disk('public')->put('imagens/test.jpg', 'fake-image-content');

        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'texto_extraido' => ['linha 1', 'linha 2'],
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GroqVisionService();
        $result = $service->analyzeScreenshot('imagens/test.jpg');

        $this->assertSame(['linha 1', 'linha 2'], $result['texto_extraido']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.groq.com/openai/v1/chat/completions'
                && $request->hasHeader('Authorization');
        });
    }
}
