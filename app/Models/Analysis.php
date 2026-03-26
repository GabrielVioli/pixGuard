<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Analysis extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'pix_key', 'type', 'amount',
        'proof_path', 'metadata', 'risk_score', 'risk_level'
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
    ];
}
