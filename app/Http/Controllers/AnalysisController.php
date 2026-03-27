<?php

namespace App\Http\Controllers;

use App\Actions\StoreAnalysisAction;
use App\Http\Requests\GeralValidateRequest;
use App\Services\ScoreEngine\RouterExtract;
use App\Services\User\analysisScreenshotService;
use App\Services\User\PixFormatService;

class AnalysisController extends Controller
{


    protected analysisScreenshotService $imageService;
    protected StoreAnalysisAction $storeAnalysisAction;
    protected PixFormatService $pixFormatService;
    protected RouterExtract $scoreEngineService;


    public function __construct
    (
        analysisScreenshotService $imageService,
        StoreAnalysisAction       $storeAnalysisAction,
        PixFormatService          $pixFormatService,
        RouterExtract $scoreEngineService
    )
    {


        $this->imageService = $imageService;
        $this->storeAnalysisAction = $storeAnalysisAction;
        $this->pixFormatService = $pixFormatService;
        $this->scoreEngineService = $scoreEngineService;

    }

    public function geralForm() {
        return view("main");
    }
    public function verify(GeralValidateRequest $request)
    {
        $input = $request->validated();

        if (!$request->hasFile('screenshot')) {
            return back()->withErrors(['screenshot' => 'O print da conversa é obrigatório.']);
        }

        $result = $this->imageService->AnalysisScreenshot($request->file('screenshot'));

        if (!$result || isset($result['error'])) {
            return back()->withErrors(['api' => 'Não foi possível analisar o contexto agora. Tente novamente.']);
        }


        $type = $this->pixFormatService->verifyFormatPix($input['pix_key']);


        $analysisData = [
            'name'         => $input['name'],
            'pix_key'      => $input['pix_key'],
            'amount'       => $input['amount'],
            'type'         => $type,
            'proof_path'   => $request->file('screenshot')->store('screenshots', 'public'),
            'metadata'     => $result,
            'ai_result'    => $result
        ];

        $score = $this->scoreEngineService->RouterExtract($analysisData);

        $analysis = $this->storeAnalysisAction->execute($analysisData);

        return dd($score);

    }
}
