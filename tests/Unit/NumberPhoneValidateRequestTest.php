<?php

namespace Tests\Unit;

use App\Http\Requests\NumberPhoneValidateRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class NumberPhoneValidateRequestTest extends TestCase
{
    public function test_phone_validation_accepts_valid_phone(): void
    {
        $rules = (new NumberPhoneValidateRequest())->rules();

        $validator = Validator::make(
            ['phone' => '(11) 90000-0000'],
            $rules
        );

        $this->assertTrue($validator->passes());
    }

    public function test_phone_validation_rejects_empty_phone(): void
    {
        $rules = (new NumberPhoneValidateRequest())->rules();

        $validator = Validator::make(
            ['phone' => ''],
            $rules
        );

        $this->assertTrue($validator->fails());
    }
}
