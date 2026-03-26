<?php

namespace Tests\Unit;

use App\Http\Requests\Sandbox\PhoneNumberValidationRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PhoneNumberValidationRequestTest extends TestCase
{
    public function test_phone_validation_accepts_valid_phone(): void
    {
        $rules = (new PhoneNumberValidationRequest())->rules();

        $validator = Validator::make(
            ['phone' => '(11) 90000-0000'],
            $rules
        );

        $this->assertTrue($validator->passes());
    }

    public function test_phone_validation_rejects_empty_phone(): void
    {
        $rules = (new PhoneNumberValidationRequest())->rules();

        $validator = Validator::make(
            ['phone' => ''],
            $rules
        );

        $this->assertTrue($validator->fails());
    }
}
