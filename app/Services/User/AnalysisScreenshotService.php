<?php

namespace App\Services\User;
use App\Services\Api\GroqApi\GroqVisionService;
use App\Services\Api\GroqApi\GroqAnalysisService;
use Illuminate\Http\UploadedFile;
class analysisScreenshotService
{

    protected GroqVisionService $groqVisionService;
    protected  GroqAnalysisService $analysisService;

    public function __construct(GroqAnalysisService $analysisService, GroqVisionService $groqVisionService) {
        $this->groqVisionService = $groqVisionService;
        $this->analysisService = $analysisService;
    }
    public function analysisScreenshot(UploadedFile $file) {

        $storedPath = $file->store('screenshots', 'public');

        $visionResponse = $this->groqVisionService->analyzeScreenshot($storedPath);

        $text = $visionResponse['texto_extraido'] ?? [];

        return $this->analysisService->evaluateContextRisk($text);
    }
}
