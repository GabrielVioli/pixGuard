<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
class GroqVisionService
{

    private string $model = 'meta-llama/llama-4-scout-17b-16e-instruct';
    private string $apiKey;
    public function __construct() {
        $this->apiKey = config('services.groq.key');
    }

    public function analyzeScreenshot(string $path) {
        $fileContent = Storage::disk('public')->get($path);
        $base64Image = base64_encode($fileContent);

        $response = Http::withToken($this->apiKey)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Você é um extrator de texto (OCR) de alta precisão. Sua tarefa é transcrever literalmente todo o texto visível na imagem, sem resumos, sem explicações e sem opiniões. Responda apenas no formato JSON.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => 'Retorne um JSON com a chave "texto_extraido" contendo a transcrição linha por linha da imagem.'],
                            ['type' => 'image_url', 'image_url' => ['url' => "data:image/jpeg;base64,{$base64Image}"]]
                        ]
                    ]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

        $content = $response->json('choices.0.message.content');

        return json_decode($content, true);
    }

}
