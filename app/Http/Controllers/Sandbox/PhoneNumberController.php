<?php

namespace App\Http\Controllers\Sandbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sandbox\PhoneNumberValidationRequest;
use App\Models\PhoneAnalysis;
use App\Integrations\AbstractApi\PhoneClient;

class PhoneNumberController extends Controller
{
    private PhoneClient $phoneExtrator;

    public function __construct(PhoneClient $phoneExtrator)
    {
        $this->phoneExtrator = $phoneExtrator;
    }

    public function formNumber()
    {
        return view('formNumberPhone');
    }

    public function getPhone(PhoneNumberValidationRequest $request)
    {
        $validatedData = $request->validated();

        $analysis = $this->phoneExtrator->analysisNumber($validatedData['phone']);

        $phoneAnalysisData = [
            'phone_number' => $analysis['phone_number'],
            'region' => $analysis['phone_location']['region'],
            'line_type' => $analysis['phone_carrier']['line_type'],
            'is_voip' => $analysis['phone_validation']['is_voip'],
            'is_valid' => $analysis['phone_validation']['is_valid'],
            'line_status' => $analysis['phone_validation']['line_status'],
            'risk_level' => $analysis['phone_risk']['risk_level'],
            'is_disposable' => $analysis['phone_risk']['is_disposable'],
            'is_abuse_detected' => $analysis['phone_risk']['is_abuse_detected'],
        ];

        $record = PhoneAnalysis::create($phoneAnalysisData);

        return redirect()->back()->with('success', 'AnÃ¡lise salva com sucesso. ID: ' . $record->id);
    }
}
