<script src="https://cdn.tailwindcss.com"></script>
<div class="p-8 max-w-lg mx-auto">
    <h1 class="text-xl font-bold mb-4">Resultado da Análise</h1>

    <div class="p-6 rounded-lg border-2 {{ $riskResult['final_score'] > 60 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200' }}">
        <div class="text-4_xl font-black">{{ $riskResult['final_score'] }}/100</div>
        <div class="font-bold uppercase">{{ $riskResult['nivel'] }}</div>
    </div>

    <ul class="mt-6 space-y-2">
        @foreach($riskResult['flags'] as $flag)
            <li class="text-sm text-gray-700">• {{ $flag }}</li>
        @endforeach
    </ul>

    <a href="/GeralForm" class="mt-8 block text-blue-600 underline text-sm">Fazer outra consulta</a>
</div>
