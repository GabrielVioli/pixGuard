<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailAnalysis extends Model
{
    use HasFactory;

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
        'is_disposable' => 'boolean',
        'is_free_email' => 'boolean',
        'quality_score' => 'decimal:2',
        'domain_age_days' => 'integer',
        'total_breaches' => 'integer',
    ];
}
