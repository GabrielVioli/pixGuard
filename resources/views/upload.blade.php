<!DOCTYPE html>
<html>
<head>
    <title>Upload de Imagem</title>
</head>
<body>

<h2>Enviar imagem</h2>

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

<form action="{{route('store')}}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="image" required>
    <br><br>
    <button type="submit">Enviar e Analisar</button>
</form>
</body>
</html>
