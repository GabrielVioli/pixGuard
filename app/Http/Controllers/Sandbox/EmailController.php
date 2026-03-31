<?php

namespace App\Http\Controllers\Sandbox;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sandbox\EmailValidationRequest;
use App\Integrations\AbstractApi\EmailClient;

class EmailController extends Controller
{
    protected EmailClient $emailClient;

    public function __construct(EmailClient $emailClient)
    {
        $this->emailClient = $emailClient;
    }

    public function emailForm()
    {
        return view('email');
    }

    public function getEmail(EmailValidationRequest $request)
    {
        $validateEmail = $request->validated();

        $analysis = $this->emailClient->analysisEmail($validateEmail['email']);

        $emailAnalysisData = [
            'email_address' => data_get($analysis, 'email_address'),
            'deliverability' => data_get($analysis, 'email_deliverability.status'),
            'quality_score' => data_get($analysis, 'email_quality.score'),
            'is_disposable' => data_get($analysis, 'email_quality.is_disposable', false),
            'is_free_email' => data_get($analysis, 'email_quality.is_free_email', false),
            'risk_status' => data_get($analysis, 'email_risk.address_risk_status'),
            'domain_age_days' => data_get($analysis, 'email_domain.domain_age'),
            'total_breaches' => data_get($analysis, 'email_breaches.total_breaches', 0),
        ];

        return response()->json($emailAnalysisData);
    }
}
