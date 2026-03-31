<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CNPJ</title>
</head>
<body>
<form method="POST" action="{{ route('sandbox.cnpj.send') }}">
    @csrf
    <label for="cnpj">CNPJ</label>
    <input id="cnpj" name="cnpj" type="text" placeholder="00.000.000/0000-00" required>
    <button type="submit">Enviar</button>
</form>
</body>
</html>
