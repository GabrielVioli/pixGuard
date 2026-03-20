<?php

namespace Tests\Feature;

use App\Http\Requests\ImageValidateRequest;
use App\Services\Api\GroqApi\GroqAnalysisService;
use App\Services\Api\GroqApi\GroqVisionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PixAnalysisControllerTest extends TestCase
{
    public function test_show_form_returns_upload_view(): void
    {
        $response = $this->get('/form');

        $response->assertOk();
        $response->assertViewIs('upload');
    }

    public function test_store_returns_score_payload(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('comprovante.jpg', 10, 'image/jpeg');

        $this->mock(GroqVisionService::class, function ($mock) {
            $mock->shouldReceive('analyzeScreenshot')
                ->once()
                ->andReturn([
                    'texto_extraido' => ['linha 1'],
                ]);
        });

        $this->mock(GroqAnalysisService::class, function ($mock) {
            $mock->shouldReceive('evaluateContextRisk')
                ->once()
                ->andReturn([
                    'score_contexto' => 10,
                    'classificacao' => 'Seguro',
                    'motivo' => 'Sem sinais de risco.',
                    'gatilhos_encontrados' => [],
                ]);
        });

        $request = ImageValidateRequest::create(
            '/upload',
            'POST',
            [],
            [],
            ['image' => $file]
        );

        $response = $this->app->make(\App\Http\Controllers\PixAnalysisController::class)
            ->store($request);

        $payload = $response->getData(true);

        $this->assertSame('success', $payload['status']);
        $this->assertArrayHasKey('caminho_arquivo', $payload);
        $this->assertSame(['linha 1'], $payload['dossie']['transcricao']);
        $this->assertSame('Seguro', $payload['dossie']['analise_risco']['classificacao']);
    }
}
