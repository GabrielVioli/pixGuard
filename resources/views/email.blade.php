<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email</title>
</head>
<body>
<form method="POST" action="{{ route('getEmail') }}">
    @csrf
    <label for="phone">email</label>
    <input id="email" name="email" type="email" placeholder="seuemail@example.com" required>
    <button type="submit">Enviar</button>
</form>
</body>
</html>
