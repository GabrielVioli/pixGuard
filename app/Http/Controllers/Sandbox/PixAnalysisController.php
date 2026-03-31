<?php

namespace App\Http\Controllers\Sandbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageValidateRequest;
use App\Integrations\Groq\GroqChatClient;
use App\Integrations\Groq\GroqVisionClient;

class PixAnalysisController extends Controller
{
    private GroqVisionClient $visionClient;
    private GroqChatClient $chatClient;

    public function __construct(GroqVisionClient $visionClient, GroqChatClient $chatClient)
    {
        $this->visionClient = $visionClient;
        $this->chatClient = $chatClient;
    }

    public function showForm()
    {
        return view('upload');
    }

    public function store(ImageValidateRequest $request)
    {
        $path = $request->file('image')->store('imagens', 'public');

        $resultadoOCR = $this->visionClient->analyzeScreenshot($path);
        $linhasDeTexto = $resultadoOCR['texto_extraido'] ?? [];

        $resultadoScore = $this->chatClient->evaluateContextRisk($linhasDeTexto);

        return response()->json([
            'status' => 'success',
            'caminho_arquivo' => asset('storage/' . $path),
            'dossie' => [
                'transcricao' => $linhasDeTexto,
                'analise_risco' => $resultadoScore,
            ],
        ]);
    }
}
