<?php

namespace App\Http\Controllers\Sandbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sandbox\CnpjValidationRequest;
use App\Integrations\BrasilApi\CnpjClient;

class CnpjController extends Controller
{
    protected CnpjClient $cnpjClient;

    public function __construct(CnpjClient $cnpjClient)
    {
        $this->cnpjClient = $cnpjClient;
    }

    public function formCnpj()
    {
        return view('Cnpj');
    }

    public function getCnpj(CnpjValidationRequest $request)
    {
        $validateCnpj = $request->input('cnpj');
        $data = $this->cnpjClient->extract($validateCnpj);

        $cnpjAnalysisData = [
            'cnpj' => $validateCnpj,
            'razao_social' => data_get($data, 'razao_social'),
            'situacao' => data_get($data, 'descricao_situacao_cadastral'),
            'data_abertura' => data_get($data, 'data_inicio_atividade'),
            'cnae_descricao' => data_get($data, 'cnae_fiscal_descricao'),
            'socios' => data_get($data, 'qsa.*.nome_socio', []),
        ];

        return response()->json($cnpjAnalysisData);
    }
}
