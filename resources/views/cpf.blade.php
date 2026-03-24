<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CPF</title>
</head>
<body>
<form method="POST" action="{{ route('getCpf') }}">
    @csrf
    <label for="cpf">CPF</label>
    <input id="cpf" name="cpf" type="text" placeholder="000.000.000-00" required>
    <button type="submit">Enviar</button>
</form>
</body>
</html>
