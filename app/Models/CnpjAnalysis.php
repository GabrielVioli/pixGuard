<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CnpjAnalysis extends Model
{
    protected $fillable = [
        'cnpj',
        'razao_social',
        'situacao',
        'data_abertura',
        'cnae_descricao',
        'socios',
    ];

    protected $casts = [
        'socios' => 'array',
    ];
}
