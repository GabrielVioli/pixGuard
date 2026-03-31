<?php

namespace App\Http\Controllers\Sandbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sandbox\CnpjValidationRequest;
use App\Models\CnpjAnalysis;
use App\Integrations\BrasilApi\CnpjClient;
use Illuminate\Support\Facades\Schema;

class CnpjController extends Controller
{
    protected CnpjClient $cnpjExtrator;

    public function __construct(CnpjClient $cnpjExtrator)
    {
        $this->cnpjExtrator = $cnpjExtrator;
    }

    public function formCnpj()
    {
        return view('Cnpj');
    }

    public function getCnpj(CnpjValidationRequest $request)
    {
        $validateCnpj = $request->input('cnpj');
        $data = $this->cnpjExtrator->extract($validateCnpj);

        $cnpjAnalysisData = [
            'cnpj' => $validateCnpj,
            'razao_social' => data_get($data, 'razao_social'),
            'situacao' => data_get($data, 'descricao_situacao_cadastral'),
            'data_abertura' => data_get($data, 'data_inicio_atividade'),
            'cnae_descricao' => data_get($data, 'cnae_fiscal_descricao'),
            'socios' => data_get($data, 'qsa.*.nome_socio', []),
        ];

        if (Schema::hasTable('cnpj_analyses')) {
            CnpjAnalysis::create($cnpjAnalysisData);
        }

        return response()->json($data);
    }
}
