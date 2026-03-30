<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Analysis extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'pix_key_hash',
        'type',
        'amount',
        'proof_path',
        'metadata',
        'details',
        'risk_score',
        'risk_level'
    ];

    protected $casts = [
        'metadata' => 'array',
        'details' => 'array',
        'amount' => 'decimal:2',
    ];
}
