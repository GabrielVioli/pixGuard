<?php

namespace App\Services\ScoreEngine;

use App\Services\User\PixFormatService;
use App\Services\Api\AbstractApi\EmailExtrator;
use App\Services\Api\CpfHub\CpfExtrator;
use App\Services\Api\AbstractApi\PhoneExtrator;
use App\Services\Api\BrasilApi\CnpjExtrator;
class RouterExtract
{

    public function __construct(protected PixFormatService $pixFormatService,
                                protected  EmailExtrator $emailExtrator,
                                protected  CpfExtrator $cpfExtrator,
                                protected PhoneExtrator $phoneExtrator,
                                protected CnpjExtrator $cnpjExtrator) {}

    public function RouterExtract(array $data) {
        $format = $this->pixFormatService->verifyFormatPix($data['pix_key']);

        $apiResponse = match ($format) {
            'EMAIL' => $this->emailExtrator->analysisEmail($data['pix_key']),
            'CPF' => $this->cpfExtrator->extract($data['pix_key']),
            'PHONE' => $this->phoneExtrator->analysisNumber($data['pix_key']),
            'CNPJ' => $this->cnpjExtrator->extract($data['pix_key']),

            default =>  throw new \InvalidArgumentException('Tipo de chave desconhecida')
        };

        return [
            'key_type' => $format,
            'raw_data' => $apiResponse
        ];
    }
}
