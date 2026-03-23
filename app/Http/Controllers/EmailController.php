<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailValidadeRequest;
use App\Services\Api\AbstractApi\EmailExtrator;
use App\Models\EmailAnalysis;
class EmailController extends Controller
{
    protected EmailExtrator $emailExtrator;


    public function __construct(EmailExtrator $emailExtrator) {
        $this->emailExtrator = $emailExtrator;
    }

    public function emailForm() {
        return view('email');
    }
    public function getEmail(EmailValidadeRequest $request) {
        $validateEmail = $request->validated();

        $analysis = $this->emailExtrator->analysisEmail($validateEmail['email']);

        $emailAnalysisData = [
            'email_address'   => data_get($analysis, 'email_address'),
            'deliverability'  => data_get($analysis, 'email_deliverability.status'),
            'quality_score'   => data_get($analysis, 'email_quality.score'),
            'is_disposable'   => data_get($analysis, 'email_quality.is_disposable', false),
            'is_free_email'   => data_get($analysis, 'email_quality.is_free_email', false),
            'risk_status'     => data_get($analysis, 'email_risk.address_risk_status'),
            'domain_age_days' => data_get($analysis, 'email_domain.domain_age'),
            'total_breaches'  => data_get($analysis, 'email_breaches.total_breaches', 0),
        ];

        $record = EmailAnalysis::create($emailAnalysisData);

        return $record;
    }
}
