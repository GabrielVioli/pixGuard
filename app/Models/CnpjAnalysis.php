<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\CnpjValidateRequest;
class CnpjAnalysis extends Model
{

    protected $fillable = [
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
