<?php

namespace App\Actions;
use App\Models\Analysis;
class StoreAnalysisAction
{
    public function execute(array $data) {
        return Analysis::create([
            'name'       => $data['name'] ?? null,
            'pix_key'    => $data['pix_key'],
            'type'       => $data['type'],
            'amount'     => $data['amount'] ?? 0,
            'proof_path' => $data['proof_path'] ?? null,
            'metadata'   => $data['metadata'] ?? [],
            'risk_score' => $data['ai_result']['score_contexto'] ?? 0,
            'risk_level' => $data['ai_result']['classificacao'] ?? 'Seguro',
        ]);
    }
}
