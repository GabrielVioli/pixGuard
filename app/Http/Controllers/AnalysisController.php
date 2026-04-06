<?php

namespace App\Http\Controllers;

use App\Actions\StoreAnalysisAction;
use App\Engines\Risk\RiskEvaluator;
use App\Http\Requests\GeralValidateRequest;
use App\Services\Extraction\DataOrchestrator;
use App\Services\Vision\ScreenshotAnalyzer;
use App\Support\PixFormatValidator;

class AnalysisController extends Controller
{
    public function geralForm() {
        return view("main");
    }

    public function __construct(
        protected ScreenshotAnalyzer $screenshotAnalyzer,
        protected StoreAnalysisAction $storeAnalysisAction,
        protected PixFormatValidator $pixFormatValidator,
        protected DataOrchestrator $dataOrchestrator,
        protected RiskEvaluator $riskEvaluator,
    ) {}

    public function verify(GeralValidateRequest $request)
    {
        $validated = $request->validated();

        if (!$request->hasFile('screenshot')) {
            return back()->withErrors(['screenshot' => 'O print da conversa é obrigatório.']);
        }

        $contextAssessment = $this->screenshotAnalyzer->analyze($request->file('screenshot'));

        if (!$contextAssessment || isset($contextAssessment['error'])) {
            return back()->withErrors(['api' => 'Falha na análise de contexto.']);
        }

        $pixKeyType = $this->pixFormatValidator->verifyFormatPix($validated['pix_key']);

        $analysisPayload = [
            'name'       => $validated['name'],
            'pix_key'    => $validated['pix_key'],
            'amount'     => $validated['amount'],
            'key_type'   => $pixKeyType,
            'proof_path' => $request->file('screenshot')->store('screenshots', 'public'),
        ];

        $riskAssessment = $this->riskEvaluator->analyze(
            $this->dataOrchestrator->orchestrate($analysisPayload),
            $contextAssessment
        );

        $veredict = $riskAssessment['veredict'] ?? [];
        $evidences = $riskAssessment['evidences'] ?? [];

        $this->storeAnalysisAction->execute([
            'pix_key'    => $validated['pix_key'],
            'type'       => $pixKeyType,
            'name'       => $validated['name'],
            'amount'     => $validated['amount'],
            'score'      => data_get($veredict, 'final_score', 0),
            'risk_level' => data_get($veredict, 'risk_level', 'Atenção'),
            'details'    => data_get($evidences, 'flags', []),
            'metadata'   => $riskAssessment['metadata'] ?? [],
            'proof_path' => $analysisPayload['proof_path']
        ]);

        return view('dashboard.resultado', ['riskResult' => $riskAssessment]);
    }
}
