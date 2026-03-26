<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PixGuard - Nova Análise</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 text-center">PixGuard 🛡️</h1>

    <form action="{{ route('pix.verify') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Nome do Destinatário</label>
            <input type="text" name="name" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Chave PIX (CPF, CNPJ, E-mail ou Telefone)</label>
            <input type="text" name="pix_key" required placeholder="000.000.000-00" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Valor da Transação (R$)</label>
            <input type="number" step="0.01" name="amount" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Anexo de Print (Comprovante/Conversa)</label>
            <input type="file" name="screenshot" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
        </div>

        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition duration-200 font-bold">
            Iniciar Análise de Risco
        </button>
    </form>
</div>

</body>
</html>
