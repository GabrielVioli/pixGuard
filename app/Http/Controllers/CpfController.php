<?php

namespace App\Http\Controllers;

use App\Http\Requests\CpfValidateRequest;
use App\Models\CpfAnalysis;
use Illuminate\Http\Request;
use App\Services\Api\CpfHub\CpfExtrator;
class CpfController extends Controller
{
    protected CpfExtrator $cpfExtrator;

    public function __construct(CpfExtrator $cpfExtrator) {
        $this->cpfExtrator = $cpfExtrator;
    }

    public function formCpf() {
        return view('cpf');
    }

    public function getCpf(CpfValidateRequest $request) {
        $validateCpf = $request->validated('cpf');
        $data = $this->cpfExtrator->extract($validateCpf);

        $record = CpfAnalysis::create($data);

    }
}
