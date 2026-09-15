<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Login | Walter productos químicos</title>
    <!-- Estilos-->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <!-- Favicon -->
    <!-- <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"> -->
</head>
<body>

    <main class="panel">
        @if ($errors->any())
            <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <strong>[!] Error:</strong> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div style="color: #BBEDC0"; margin: 8px>
                <p>Registro de empleados | Walter Enterprises</p>
            </div>
            <h2>Iniciar sesión</h2>

            <!--usuario -->
            <label for="correo_user" class="form-label">Correo:</label>
            <input
                type="email"
                id="correo_user"
                name="email"
                value="{{ old('email') }}"
                placeholder="ej. admin@example.com"
                required
                class="form-input"
                autocomplete="username"
            >

            <!--contraseña -->
            <label for="contrasena" class="form-label">Contraseña:</label>
            <input
                type="password"
                id="contrasena"
                name="password"
                placeholder="Escriba su contraseña..."
                required
                minlength="6"
                maxlength="30"
                class="form-input"
                autocomplete="current-password"
            >
            <button type="submit" class="button-login" id="btnLogin">Iniciar Sesión</button>
        </form>
    </main>

    <footer class="version">
        <small>Versión 1.2</small>
    </footer>
</body>
</html>
