<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneAnalysis extends Model
{
    protected $fillable = [
        'phone_number',
        'region',
        'line_type',
        'is_voip',
        'is_valid',
        'line_status',
        'risk_level',
        'is_disposable',
        'is_abuse_detected',
        ];
}
