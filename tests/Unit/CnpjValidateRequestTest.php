<?php

namespace Tests\Unit;

use App\Http\Requests\CnpjValidateRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class CnpjValidateRequestTest extends TestCase
{
    public function test_cnpj_validation_accepts_valid_format(): void
    {
        $rules = (new CnpjValidateRequest())->rules();

        $validator = Validator::make(
            ['cnpj' => '12.345.678/0001-95'],
            $rules
        );

        $this->assertTrue($validator->passes());
    }

    public function test_cnpj_validation_rejects_invalid_format(): void
    {
        $rules = (new CnpjValidateRequest())->rules();

        $validator = Validator::make(
            ['cnpj' => '123'],
            $rules
        );

        $this->assertTrue($validator->fails());
    }
}
