<script src="https://cdn.tailwindcss.com"></script>

<div class="p-8 max-w-lg mx-auto bg-gray-50 min-h-screen">
    <h1 class="text-xl font-bold mb-4 text-gray-800">Relatório PixGuard</h1>

    @php
        $veredict = $riskResult['veredict'] ?? [
            'final_score' => 0,
            'risk_level' => 'AtenÃ§Ã£o',
            'risk_color' => 'amber',
            'recommendation' => 'N/A',
        ];
        $audit = $riskResult['audit'] ?? [];
        $flags = collect($riskResult['evidences']['flags'] ?? []);
        $metadata = $riskResult['metadata'] ?? [];

        // Helper para extrair INFO das flags
        $getInfo = fn($key) => str_replace($key, '', $flags->first(fn($f) => str_contains($f, $key)) ?? '');

        // Variáveis de exibição
        $razao = $getInfo('INFO_RS: ');
        $doc = $metadata['cnpj'] ?? $metadata['cpf'] ?? null;
        $city = $getInfo('INFO_CITY: ');
        $cnae = $getInfo('INFO_CNAE: ');
        $socios = $getInfo('INFO_PARTNERS: ');
        $aiReasoning = data_get($riskResult, 'behavioral.ai_reasoning', 'N/A');
    @endphp

    <div class="p-6 rounded-xl border-2 shadow-sm
        {{ $veredict['risk_color'] === 'red' ? 'bg-red-50 border-red-200' :
           ($veredict['risk_color'] === 'amber' ? 'bg-amber-50 border-amber-200' : 'bg-green-50 border-green-200') }}">

        <div class="flex justify-between items-start">
            <div>
                <div class="text-4xl font-black text-gray-900">{{ $veredict['final_score'] }}/100</div>
                <div class="font-bold uppercase text-sm tracking-widest opacity-70">{{ $veredict['risk_level'] }}</div>
            </div>
            <div class="text-xs text-gray-500 font-mono text-right">
                {{ $audit['analysis_at'] ?? now()->format('d/m/Y H:i') }}<br>
                {{ $audit['pix_key_type'] ?? 'N/A' }}
            </div>
        </div>

        <p class="mt-4 text-sm font-medium leading-relaxed text-gray-800">{{ $veredict['recommendation'] }}</p>

        <div class="mt-6 pt-4 border-t border-gray-200/50 text-sm text-gray-700 space-y-2">
            @if($razao)
                <p><strong>Razão Social:</strong> {{ $razao }}</p>
            @endif

            @if($doc)
                <p><strong>Documento:</strong> {{ $doc }}</p>
            @endif

            @if($city)
                <p><strong>Localidade:</strong> {{ $city }}</p>
            @endif

            @if($cnae)
                <p><strong>Atividade:</strong> {{ $cnae }}</p>
            @endif

            @if($socios)
                <p class="text-xs text-gray-500 italic"><strong>Sócios:</strong> {{ $socios }}</p>
            @endif

            @if(!$razao && !$doc && !$city && !$cnae)
                <p class="text-gray-400 italic">Dados cadastrais indisponíveis para esta consulta.</p>
            @endif
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 text-center md:text-left">Análise Técnica</h3>
        <ul class="space-y-2">
            @forelse($flags as $flag)
                @if(!str_contains($flag, 'INFO_'))
                    <li class="flex items-start gap-2 text-sm {{ str_contains(strtoupper($flag), 'CRÍTICO') ? 'text-red-700 font-bold' : 'text-gray-700' }}">
                        <span class="mt-1">•</span>
                        <span>{{ $flag }}</span>
                    </li>
                @endif
            @empty
                <li class="text-sm text-gray-400 italic">Nenhuma evidência negativa encontrada.</li>
            @endforelse
        </ul>
    </div>

    @if($aiReasoning && $aiReasoning !== 'N/A')
        <div class="mt-8 p-4 bg-gray-100 rounded-lg border border-gray-200 shadow-inner">
            <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Análise Comportamental (IA)</h4>
            <p class="text-sm text-gray-600 italic">"{{ $aiReasoning }}"</p>
        </div>
    @endif

    <div class="mt-10 flex justify-between items-center">
        <a href="/" class="text-blue-600 hover:underline text-sm font-medium">← Nova Consulta</a>
        <span class="text-[10px] text-gray-400 font-mono italic">PixGuard v1.0.4</span>
    </div>
</div>
