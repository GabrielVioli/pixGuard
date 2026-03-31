<?php

namespace App\Services\Extraction;

use App\Support\PixFormatValidator;
use App\Integrations\AbstractApi\EmailClient;
use App\Integrations\CpfHub\CpfClient;
use App\Integrations\AbstractApi\PhoneClient;
use App\Integrations\BrasilApi\CnpjClient;
class DataOrchestrator
{

    public function __construct(protected PixFormatValidator $pixFormatValidator,
                                protected  EmailClient $emailClient,
                                protected  CpfClient $cpfClient,
                                protected PhoneClient $phoneClient,
                                protected CnpjClient $cnpjClient) {}

    public function orchestrate(array $data) {
        $format = $this->pixFormatValidator->verifyFormatPix($data['pix_key']);

        $apiResponse = match ($format) {
            'EMAIL' => $this->emailClient->analysisEmail($data['pix_key']),
            'CPF' => $this->cpfClient->extract($data['pix_key']),
            'PHONE' => $this->phoneClient->analysisNumber($data['pix_key']),
            'CNPJ' => $this->cnpjClient->extract($data['pix_key']),

            default =>  throw new \InvalidArgumentException('Tipo de chave desconhecida')
        };

        return [
            'key_type' => $format,
            'raw_data' => $apiResponse
        ];
    }
}
