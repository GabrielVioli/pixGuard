<?php

namespace App\Services\Vision;
use App\Integrations\Groq\GroqVisionClient;
use App\Integrations\Groq\GroqChatClient;
use Illuminate\Http\UploadedFile;
class ScreenshotAnalyzer
{

    protected GroqVisionClient $groqVisionClient;
    protected  GroqChatClient $analysisClient;

    public function __construct(GroqChatClient $analysisClient, GroqVisionClient $groqVisionClient) {
        $this->groqVisionClient = $groqVisionClient;
        $this->analysisClient = $analysisClient;
    }
    public function analyze(UploadedFile $file) {

        $storedPath = $file->store('screenshots', 'public');

        $visionResponse = $this->groqVisionClient->analyzeScreenshot($storedPath);

        $text = $visionResponse['texto_extraido'] ?? [];

        return $this->analysisClient->evaluateContextRisk($text);
    }
}
