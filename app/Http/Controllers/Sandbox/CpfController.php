<?php

namespace App\Http\Controllers\Sandbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sandbox\CpfValidationRequest;
use App\Integrations\CpfHub\CpfClient;

class CpfController extends Controller
{
    protected CpfClient $cpfClient;

    public function __construct(CpfClient $cpfClient)
    {
        $this->cpfClient = $cpfClient;
    }

    public function formCpf()
    {
        return view('cpf');
    }

    public function getCpf(CpfValidationRequest $request)
    {
        $validateCpf = $request->validated('cpf');
        $data = $this->cpfClient->extract($validateCpf);

        return response()->json($data);
    }
}
