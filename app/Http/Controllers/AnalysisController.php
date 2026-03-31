<?php

namespace App\Http\Controllers;

use App\Actions\StoreAnalysisAction;
use App\Http\Requests\GeralValidateRequest;
use App\Engines\Risk\RiskEvaluator;
use App\Services\Extraction\DataOrchestrator;
use App\Services\Vision\ScreenshotAnalyzer;
use App\Support\PixFormatValidator;

class AnalysisController extends Controller
{


    public function geralForm() {
        return view("main");
    }

    public function __construct
    (
        protected ScreenshotAnalyzer $imageService,
        protected StoreAnalysisAction       $storeAnalysisAction,
        protected PixFormatValidator          $pixFormatService,
        protected DataOrchestrator $scoreEngineService,
        protected RiskEvaluator $assessmentService,
    )
    {}

    public function verify(GeralValidateRequest $request)
    {
        $input = $request->validated();

        if (!$request->hasFile('screenshot')) {
            return back()->withErrors(['screenshot' => 'O print da conversa é obrigatório.']);
        }

        $resultIA = $this->imageService->analyze($request->file('screenshot'));

        if (!$resultIA || isset($resultIA['error'])) {
            return back()->withErrors(['api' => 'Falha na análise de contexto.']);
        }

        $type = $this->pixFormatService->verifyFormatPix($input['pix_key']);

        $analysisData = [
            'name'       => $input['name'],
            'pix_key'    => $input['pix_key'],
            'amount'     => $input['amount'],
            'key_type'   => $type,
            'proof_path' => $request->file('screenshot')->store('screenshots', 'public'),
        ];

        $type = $this->pixFormatService->verifyFormatPix($input['pix_key']);

        $riskResult = $this->assessmentService->analyze(
            $this->scoreEngineService->orchestrate($analysisData),
            $resultIA
        );

        $this->storeAnalysisAction->execute([
            'pix_key'    => $input['pix_key'],
            'type'       => $type,
            'name'       => $input['name'],
            'amount'     => $input['amount'],
            'score'      => $riskResult['final_score'],
            'risk_level' => $riskResult['nivel'],
            'details'    => $riskResult['flags'],
            'metadata'   => $riskResult['metadata'],
            'proof_path' => $analysisData['proof_path']
        ]);

        return view('dashboard.resultado', compact('riskResult'));
    }
}
