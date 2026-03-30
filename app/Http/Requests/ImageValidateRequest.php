<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageValidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png'],
        ];
    }
}
