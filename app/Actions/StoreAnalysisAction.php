<?php

namespace App\Actions;

use App\Models\Analysis;

class StoreAnalysisAction
{

    public function execute(array $data): Analysis
    {
        return Analysis::create([
            'name'         => $data['name'] ?? null,
            'pix_key_hash' => hash('sha256', $data['pix_key']),
            'type'         => $data['type'],
            'amount'       => $data['amount'] ?? 0,
            'proof_path'   => $data['proof_path'] ?? null,
            'metadata'     => $data['metadata'] ?? [],
            'risk_score'   => $data['score'] ?? 0,
            'risk_level'   => $data['risk_level'] ?? 'Atenção',
            'details'      => $data['flags'] ?? [],
        ]);
    }
}
