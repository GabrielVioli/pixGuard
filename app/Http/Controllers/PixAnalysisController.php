<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageValidateRequest;
use App\Services\Api\GroqApi\GroqAnalysisService;
use App\Services\Api\GroqApi\GroqVisionService;

class PixAnalysisController extends Controller
{
    private GroqVisionService $groqVisionService;
    private GroqAnalysisService $groqAnalysisService;

    public function __construct(GroqVisionService $groqVisionService, GroqAnalysisService $groqAnalysisService)
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
