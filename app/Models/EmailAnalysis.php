<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailAnalysis extends Model
{
    protected $fillable = [
        'email_address',
        'deliverability',
        'quality_score',
        'is_disposable',
        'is_free_email',
        'risk_status',
        'domain_age_days',
        'total_breaches',
    ];

    protected $casts = [
        'quality_score' => 'decimal:2',
        'is_disposable' => 'boolean',
        'is_free_email' => 'boolean',
        'domain_age_days' => 'integer',
        'total_breaches' => 'integer',
    ];
}
