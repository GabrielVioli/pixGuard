<?php

namespace App\Http\Controllers;

use App\Http\Requests\CnpjValidateRequest;
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

        return $data;
    }
}
