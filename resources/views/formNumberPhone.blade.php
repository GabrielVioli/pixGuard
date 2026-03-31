<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Numero de Telefone</title>
</head>
<body>
    <form method="POST" action="{{ route('sandbox.phone.send') }}">
        @csrf
        <label for="phone">Numero de telefone</label>
        <input id="phone" name="phone" type="text" placeholder="(11) 90000-0000" required>
        <button type="submit">Enviar</button>
    </form>
</body>
</html>
