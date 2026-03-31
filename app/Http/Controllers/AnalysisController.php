<?php

namespace App\Http\Controllers;

use App\Actions\StoreAnalysisAction;
use App\Http\Requests\GeralValidateRequest;
use App\Services\ScoreEngine\RiskAssessmentService;
use App\Services\ScoreEngine\RouterExtract;
use App\Services\User\analysisScreenshotService;
use App\Services\User\PixFormatService;

class AnalysisController extends Controller
{


    public function geralForm() {
        return view("main");
    }

    public function __construct
    (
        protected analysisScreenshotService $imageService,
        protected StoreAnalysisAction       $storeAnalysisAction,
        protected PixFormatService          $pixFormatService,
        protected RouterExtract $scoreEngineService,
        protected RiskAssessmentService $assessmentService,
    )
    {}

    public function verify(GeralValidateRequest $request)
    {
        $input = $request->validated();

        if (!$request->hasFile('screenshot')) {
            return back()->withErrors(['screenshot' => 'O print da conversa é obrigatório.']);
        }

        $resultIA = $this->imageService->AnalysisScreenshot($request->file('screenshot'));

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
            $this->scoreEngineService->RouterExtract($analysisData),
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
