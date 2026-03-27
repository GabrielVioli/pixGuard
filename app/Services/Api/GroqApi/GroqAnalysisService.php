<?php

namespace App\Services\Api\GroqApi;

use Illuminate\Support\Facades\Http;

class GroqAnalysisService
{
    private string $apiKey;

    private string $model = 'llama-3.3-70b-versatile';

    public function __construct()
    {
        $this->apiKey = config('services.groq.key');
    }


    public function evaluateContextRisk(array $textoExtraido): array
    {
        $textoParaAnalisar = implode("\n", $textoExtraido);

        $systemPrompt = <<<PROMPT
Você é o Motor de ScoreEngine de Risco Antifraude do sistema PixGuard.
Sua função é analisar a transcrição de um chat e atribuir um "ScoreEngine de Contexto" estritamente baseado nestas regras:

REGRA 5.1 - SCORE DE CONTEXTO:
- Conversa Normal/Comercial (Sem risco): 0 pontos.
- Senso de Urgência / Oferta Irreal (Ex: "corre que vai acabar", "ganhe dinheiro fácil"): 30 a 49 pontos.
- Engenharia Social Ativa (Ex: chantagem, sequestro PIX, falso parente pedindo dinheiro, ameaça): 50 a 80 pontos.

REGRA 5.0 - CLASSIFICAÇÃO:
- 0 a 29: Seguro
- 30 a 59: Atenção
- 60 a 100: Alto Risco

Retorne EXCLUSIVAMENTE um formato JSON com a seguinte estrutura:
{
    "score_contexto": (int) Pontuação calculada,
    "classificacao": (string) "Seguro", "Atenção" ou "Alto Risco",
    "motivo": (string) Explicação técnica e breve da pontuação,
    "gatilhos_encontrados": (array de strings) Frases ou padrões suspeitos exatos encontrados no texto. Se nenhum, retorne array vazio.
}
PROMPT;

        $response = Http::withToken($this->apiKey)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt
                    ],
                    [
                        'role' => 'user',
                        'content' => "Analise o seguinte texto e retorne o JSON do ScoreEngine:\n\n" . $textoParaAnalisar
                    ]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

        $content = $response->json('choices.0.message.content');

        return json_decode($content, true) ?? [
            'error' => 'Falha ao analisar o contexto'
        ];
    }
}
