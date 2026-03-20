<?php

namespace App\Http\Controllers;

use App\Http\Requests\NumberPhoneValidateRequest;
use App\Services\Api\AbstractApi\PhoneExtrator;
class NumberPhoneController
{
    private PhoneExtrator $phoneExtrator;

    public function __construct(PhoneExtrator $phoneExtrator) {
        $this->phoneExtrator = $phoneExtrator;
    }

    public function formNumber() {
        return view('formNumberPhone');
    }


    public function getPhone(NumberPhoneValidateRequest $request): void{
        $validatedData = $request->validated();

        $analysis = $this->phoneExtrator->analysisNumber($validatedData['phone']);

        $phoneAnalysisData = [
            'phone_number'      => $analysis['phone_number'],
            'region'            => $analysis['phone_location']['region'],
            'line_type'         => $analysis['phone_carrier']['line_type'],
            'is_voip'           => $analysis['phone_validation']['is_voip'],
            'is_valid'          => $analysis['phone_validation']['is_valid'],
            'line_status'       => $analysis['phone_validation']['line_status'],
            'risk_level'        => $analysis['phone_risk']['risk_level'],
            'is_disposable'     => $analysis['phone_risk']['is_disposable'],
            'is_abuse_detected' => $analysis['phone_risk']['is_abuse_detected'],
        ];

        dd($phoneAnalysisData);
    }
}
