<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpfAnalysis extends Model
{
    protected $fillable = [
        'cpf',
        'nome',
        'situacao',
        'genero',
        'nascimento',
    ];

    protected $casts = [
        'nascimento' => 'date',
    ];
}
