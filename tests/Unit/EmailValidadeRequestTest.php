<?php

namespace Tests\Unit;

use App\Http\Requests\EmailValidadeRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class EmailValidadeRequestTest extends TestCase
{
    public function test_email_validation_accepts_valid_email(): void
    {
        $rules = (new EmailValidadeRequest())->rules();

        $validator = Validator::make(
            ['email' => 'usuario.teste@example.com'],
            $rules
        );

        $this->assertTrue($validator->passes());
    }

    public function test_email_validation_rejects_invalid_email(): void
    {
        $rules = (new EmailValidadeRequest())->rules();

        $validator = Validator::make(
            ['email' => 'invalido'],
            $rules
        );

        $this->assertTrue($validator->fails());
    }
}
