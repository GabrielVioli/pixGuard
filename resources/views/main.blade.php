<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PixGuard - Prevenção a Fraudes</title>
    <link rel="shortcut icon" href="icons/icon.ico" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 border border-slate-200 shadow-sm w-full max-w-md">

    <img src="icons/icon.ico" alt="Logotipo Institucional PixGuard" class="mx-auto h-16 w-auto mb-4">

    <hr class="border-slate-200 mb-6">

    <form action="{{ route('pix.verify') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-semibold text-slate-800 mb-1">Nome do Destinatário</label>
            <input type="text" name="name" required class="block w-full rounded-sm border-slate-300 shadow-sm focus:border-slate-800 focus:ring-slate-800 sm:text-sm p-2 border">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-800 mb-1">Chave PIX <span class="text-xs font-normal text-slate-500">(CPF, CNPJ, E-mail ou Celular)</span></label>
            <input type="text" name="pix_key" required placeholder="000.000.000-00" class="block w-full rounded-sm border-slate-300 shadow-sm focus:border-slate-800 focus:ring-slate-800 sm:text-sm p-2 border">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-800 mb-1">Valor da Transação (R$)</label>
            <input type="number" step="0.01" name="amount" required class="block w-full rounded-sm border-slate-300 shadow-sm focus:border-slate-800 focus:ring-slate-800 sm:text-sm p-2 border">
        </div>

        <div>
            <label class="block text-sm font-semibold text-slate-800 mb-1">Anexo de Evidência <span class="text-xs font-normal text-slate-500">(Print da Conversa)</span></label>
            <input type="file" name="screenshot" accept="image/*" class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 border border-slate-200 rounded-sm p-1">
        </div>

        <button type="submit" class="w-full bg-slate-800 text-white py-2 px-4 rounded-sm hover:bg-slate-900 transition duration-150 font-bold text-sm tracking-wide mt-2">
            PROCESSAR ANÁLISE DE RISCO
        </button>
    </form>
</div>

</body>
</html>
