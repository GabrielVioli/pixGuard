<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeralValidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'pix_key' => 'required|string|min:4|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'screenshot' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do destinatário é obrigatório.',
            'pix_key.required' => 'Informe a chave Pix para análise.',
            'amount.min' => 'O valor mínimo para análise é R$ 0,01.',
            'screenshot.image' => 'O anexo deve ser uma imagem válida.',
            'screenshot.max' => 'A imagem não pode ultrapassar 4MB.',
        ];
    }


    public function validated($key = null, $default = null)
    {
        $data = parent::validated();

        if (isset($data['pix_key'])) {
            $data['pix_key'] = trim($data['pix_key']);
        }

        return $data;
    }
}
