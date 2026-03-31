<?php

namespace App\Support;

use App\Traits\ValidateDocuments;

class PixFormatValidator
{
    use ValidateDocuments;
    public function verifyFormatPix(string $pixKey): string
    {
        $pixKey = trim($pixKey);

        if (filter_var($pixKey, FILTER_VALIDATE_EMAIL)) {
            return 'EMAIL';
        }

        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $pixKey)) {
            return 'EVP';
        }

        $digits = preg_replace('/[^0-9]/', '', $pixKey);

        if ((strlen($digits) == 11 || strlen($digits) == 13) && preg_match('/^(55)?[1-9]{2}9[0-9]{8}$/', $digits)) {
            return 'PHONE';
        }

        if (strlen($digits) == 11 && $this->isCpf($digits)) {
            return 'CPF';
        }

        if (strlen($digits) == 14 && $this->isCnpj($digits)) {
            return 'CNPJ';
        }

        return "INVALID_OR_UNKNOWN";
    }

}
