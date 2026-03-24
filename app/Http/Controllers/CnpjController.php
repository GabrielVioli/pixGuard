<?php

namespace App\Http\Controllers;

use App\Http\Requests\CnpjValidateRequest;
use App\Models\CnpjAnalysis;
use App\Services\Api\BrasilApi\CnpjExtrator;
use Illuminate\Http\Request;

class CnpjController extends Controller
{
    protected CnpjExtrator $cnpjExtrator;


    public function __construct(CnpjExtrator $cnpjExtrator) {
        $this->cnpjExtrator = $cnpjExtrator;
    }
    public function formCnpj() {
        return view("Cnpj");
    }

    public function getCnpj(CnpjValidateRequest $request)
    {
        $validateCnpj = $request->input('cnpj');
        $data = $this->cnpjExtrator->extract($validateCnpj);

        $cnpjAnalysisData = [
            'razao_social' => data_get($data, 'razao_social'),
            'situacao' => data_get($data, 'descricao_situacao_cadastral'),
            'data_abertura' => data_get($data, 'data_inicio_atividade'),
            'cnae_descricao' => data_get($data, 'cnae_fiscal_descricao'),
            'socios' => data_get($data, 'qsa.*.nome_socio', []),
        ];

        $record = CnpjAnalysis::create($cnpjAnalysisData);

    }
}
