<?php

namespace Tests\Unit\Services;

use App\Services\Api\GroqApi\GroqAnalysisService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GroqAnalysisServiceTest extends TestCase
{
    public function test_evaluate_context_risk_returns_decoded_json(): void
    {
        config(['services.groq.key' => 'test-key']);

        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'score_contexto' => 45,
                                'classificacao' => 'Atencao',
                                'motivo' => 'Urgencia identificada.',
                                'gatilhos_encontrados' => ['corre que vai acabar'],
                            ]),
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GroqAnalysisService();
        $result = $service->evaluateContextRisk(['corre que vai acabar']);

        $this->assertSame(45, $result['score_contexto']);
        $this->assertSame('Atencao', $result['classificacao']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.groq.com/openai/v1/chat/completions'
                && $request->hasHeader('Authorization');
        });
    }

    public function test_evaluate_context_risk_returns_error_on_invalid_json(): void
    {
        config(['services.groq.key' => 'test-key']);

        Http::fake([
            'https://api.groq.com/*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'invalid-json',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new GroqAnalysisService();
        $result = $service->evaluateContextRisk(['texto']);

        $this->assertSame(['error' => 'Falha ao analisar o contexto'], $result);
    }
}
