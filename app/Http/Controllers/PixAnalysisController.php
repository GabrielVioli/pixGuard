<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageValidateRequest;
use App\Integrations\Groq\GroqChatClient;
use App\Integrations\Groq\GroqVisionClient;

class PixAnalysisController extends Controller
{
    private GroqVisionClient $groqVisionService;
    private GroqChatClient $groqAnalysisService;

    public function __construct(GroqVisionClient $groqVisionService, GroqChatClient $groqAnalysisService)
    {
        $this->groqVisionService = $groqVisionService;
        $this->groqAnalysisService = $groqAnalysisService;
    }

    public function showForm()
    {
        return view('upload');
    }

    public function store(ImageValidateRequest $request)
    {
        $path = $request->file('image')->store('imagens', 'public');

        $resultadoOCR = $this->groqVisionService->analyzeScreenshot($path);
        $linhasDeTexto = $resultadoOCR['texto_extraido'] ?? [];

        $resultadoScore = $this->groqAnalysisService->evaluateContextRisk($linhasDeTexto);

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
