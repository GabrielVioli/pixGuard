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
VocÃª Ã© o Especialista em InteligÃªncia de Dados do PixGuard.
Sua missÃ£o Ã© analisar transcriÃ§Ãµes de conversas e extrair entidades para cruzamento de dados.

DIRETRIZES DE EXTRAÃ‡ÃƒO:
1. nome_titular_esperado: Identifique o nome da pessoa que o remetente afirma ser ou o favorecido mencionado para o pagamento. Se for "mÃ£e" ou "pai", tente achar o nome real. Se nÃ£o houver, retorne null.
2. banco_mencionado: Identifique se algum banco (Nubank, ItaÃº, Bradesco, etc) foi citado.
3. categoria_golpe: Classifique em: "Nenhum", "Falso Parente", "Falso Funcionario Banco", "Produto Inexistente", "Urgent/Social Engineering".

REGRAS DE SCORE (0 a 100):
- 0-29: Conversa legÃ­tima, sem pressa ou pressÃ£o.
- 30-59: UrgÃªncia leve, pedidos de dinheiro sem contexto claro.
- 60-100: PressÃ£o psicolÃ³gica, ameaÃ§a, troca de nÃºmero ou conta de terceiro (laranja).

Retorne EXCLUSIVAMENTE este JSON:
{
    "score_contexto": (int),
    "classificacao": "Seguro" | "AtenÃ§Ã£o" | "Alto Risco",
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
        return json_decode($content, true) ?? ['error' => 'Falha na anÃ¡lise'];
    }
}
