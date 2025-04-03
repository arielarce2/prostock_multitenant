<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .body {
            padding: 20px;
            text-align: left;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #aaa;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verificación de Correo Electrónico</h1>
        </div>
        <div class="body">
            <h2>Hola, {{ $user->name }}</h2>
            <p>Gracias por registrarte en <strong>ProStock</strong>. Para activar tu cuenta, por favor verifica tu correo electrónico usando el siguiente código:</p>
            <p><strong>Código de verificación: {{ $verificationCode }}</strong></p>
            <p>Si no te registraste, ignora este correo.</p>
            <a href="{{ url('verify-email/' . $verificationCode) }}" class="button">Verificar Correo Electrónico</a>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} ProStock. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>