<?php

namespace App\Actions;

use App\Models\Analysis;

class StoreAnalysisAction
{
    /**
     * Persiste a análise seguindo as normas de LGPD e o Score Consolidado.
     */
    public function execute(array $data): Analysis
    {
        return Analysis::create([
            'name'         => $data['name'] ?? null,
            // O nome aqui DEVE ser igual ao da migration: pix_key_hash
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
