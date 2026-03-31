<?php

namespace App\Integrations\Groq;

use Illuminate\Support\Facades\Http;

class GroqChatClient
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
Você é o Especialista em Inteligência de Dados do PixGuard.
Sua missão é analisar transcrições de conversas e extrair entidades para cruzamento de dados.

DIRETRIZES DE EXTRAÇÃO:
1. nome_titular_esperado: Identifique o nome da pessoa que o remetente afirma ser ou o favorecido mencionado para o pagamento. Se for "mãe" ou "pai", tente achar o nome real. Se não houver, retorne null.
2. banco_mencionado: Identifique se algum banco (Nubank, Itaú, Bradesco, etc) foi citado.
3. categoria_golpe: Classifique em: "Nenhum", "Falso Parente", "Falso Funcionário Banco", "Produto Inexistente", "Urgent/Social Engineering".

REGRAS DE SCORE (0 a 100):
- 0-29: Conversa legítima, sem pressa ou pressão.
- 30-59: Urgência leve, pedidos de dinheiro sem contexto claro.
- 60-100: Pressão psicológica, ameaça, troca de número ou conta de terceiro (laranja).

Retorne EXCLUSIVAMENTE este JSON:
{
    "score_contexto": (int),
    "classificacao": "Seguro" | "Atenção" | "Alto Risco",
    "nome_titular_esperado": (string|null) "Nome Completo Encontrado",
    "banco_mencionado": (string|null),
    "categoria_golpe": (string),
    "motivo": (string) "Resumo curto",
    "gatilhos_encontrados": []
}
PROMPT;

        $response = Http::withToken($this->apiKey)
            ->post('https://api.groq.com/openai/v1/chat/completions', [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "Analise:\n\n" . $textoParaAnalisar]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

        $content = $response->json('choices.0.message.content');
        return json_decode($content, true) ?? ['error' => 'Falha na análise'];
    }
}
