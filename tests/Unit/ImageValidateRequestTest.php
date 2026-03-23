<?php

namespace Tests\Unit;

use App\Http\Requests\ImageValidateRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ImageValidateRequestTest extends TestCase
{
    public function test_image_validation_accepts_jpeg(): void
    {
        $rules = (new ImageValidateRequest())->rules();

        $validator = Validator::make(
            ['image' => UploadedFile::fake()->image('comprovante.jpg', 10, 10)],
            $rules
        );

        $this->assertTrue($validator->passes());
    }

    public function test_image_validation_rejects_non_image(): void
    {
        $rules = (new ImageValidateRequest())->rules();

        $validator = Validator::make(
            ['image' => UploadedFile::fake()->create('arquivo.pdf', 10, 'application/pdf')],
            $rules
        );

        $this->assertTrue($validator->fails());
    }
}
