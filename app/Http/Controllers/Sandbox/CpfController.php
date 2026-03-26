<?php

namespace App\Http\Controllers\Sandbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sandbox\CpfValidationRequest;
use App\Models\CpfAnalysis;
use App\Services\Api\CpfHub\CpfExtrator;

class CpfController extends Controller
{
    protected CpfExtrator $cpfExtrator;

    public function __construct(CpfExtrator $cpfExtrator)
    {
        $this->cpfExtrator = $cpfExtrator;
    }

    public function formCpf()
    {
        return view('cpf');
    }

    public function getCpf(CpfValidationRequest $request)
    {
        $validateCpf = $request->validated('cpf');
        $data = $this->cpfExtrator->extract($validateCpf);

        CpfAnalysis::create($data);
    }
}
