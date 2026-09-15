<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard | Quimicos Walter</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    @vite('resources/js/app.js')
</head>
<body class="bg-neutral-950 text-neutral-200 font-sans">
    <main class="p-10 max-w-7xl mx-auto space-y-10">
        <!-- Encabezado -->
    <section class="header">
        <div class="header-content">
            <div class="header-title" style="color: #BBEDC0">
                <h1>Walter | Empresa productos químicos</h1>
                <p>Gestión de usuarios y asistencia</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">Cerrar sesión</button>
            </form>
        </div>
    </section>


        <!-- Cuadros de resumen -->
        <section class="summary-grid">
            <div id="card-atrasos" class="summary-grid">Atrasos: 0</div>
            <div id="card-salidas" class="summary-grid">Salidas anticipadas: 0</div>
            <div id="card-inasistencias" class="summary-grid">Inasistencias: 0</div>
        </section>

        <!-- Panel de Usuarios -->
        <section class="panel">
            <div class="panel">
                <h2 class="text-2xl font-bold">Usuarios</h2>
                <button class="bg-neutral-800 px-4 py-2 rounded-lg hover:bg-neutral-700 transition"
                        onclick="filtrarUsuarios('todos')">
                    <svg  xmlns="http://www.w3.org/2000/svg" width="16" height="16"  
                        fill="currentColor" viewBox="0 0 24 24" >
                        <!--Boxicons v3.0.8 https://boxicons.com | License  https://docs.boxicons.com/free-->
                        <path d="M19.07 4.93a9.9 9.9 0 0 0-3.18-2.14A9.95 9.95 0 0 0 12 2v2c1.08 0 2.13.21 3.11.63.95.4 1.81.98 2.54 1.71s1.31 1.59 1.72 2.54c.42.99.63 2.03.63 3.11s-.21 2.13-.63 3.11c-.4.95-.98 1.81-1.72 2.54-.17.17-.34.32-.52.48L15 15.99v6h6l-2.45-2.45c.18-.15.36-.31.52-.48.92-.92 1.64-1.99 2.14-3.18.52-1.23.79-2.54.79-3.89s-.26-2.66-.79-3.89a9.9 9.9 0 0 0-2.14-3.18ZM4.93 19.07c.92.92 1.99 1.64 3.18 2.14 1.23.52 2.54.79 3.89.79v-2a7.9 7.9 0 0 1-3.11-.63c-.95-.4-1.81-.98-2.54-1.71s-1.31-1.59-1.72-2.54c-.42-.99-.63-2.03-.63-3.11s.21-2.13.63-3.11c.4-.95.98-1.81 1.72-2.54.17-.17.34-.32.52-.48L9 8.01V2H3l2.45 2.45c-.18.15-.36.31-.52.48-.92.92-1.64 1.99-2.14 3.18C2.27 9.34 2 10.65 2 12s.26 2.66.79 3.89c.5 1.19 1.22 2.26 2.14 3.18"></path>
                        </svg>
                </button>
            </div>

        <!-- Panel de Crear Usuario -->
        <section class="panel">
            <h2>Añadir Usuario</h2>

            <!-- Mensajes dinámicos -->
            <div id="mensaje-exito" class="hidden mb-4 p-3 rounded bg-green-700 text-green-100"></div>
            <div id="mensaje-error" class="hidden mb-4 p-3 rounded bg-red-700 text-red-100"></div>

            <form id="form-usuario">
                <div>
                    <label>Nombre</label>
                    <input type="text" name="name" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div>
                    <label>Contraseña</label>
                    <input type="password" name="password" minlength="8" required>
                </div>
                <div>
                    <label>Estado</label>
                    <select name="status">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <button type="submit">Guardar Usuario</button>
            </form>
        </section>



            <div class="flex flex-wrap gap-3 mb-6">
                <button class="bg-neutral-800 px-4 py-2 rounded-lg hover:bg-neutral-700 transition" onclick="filtrarUsuarios('todos')">Todos</button>
                <button class="bg-neutral-800 px-4 py-2 rounded-lg hover:bg-neutral-700 transition" onclick="filtrarUsuarios('activo')">Activos</button>
                <button class="bg-neutral-800 px-4 py-2 rounded-lg hover:bg-neutral-700 transition" onclick="filtrarUsuarios('inactivo')">Inactivos</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-neutral-800">
                            <th class="p-3 border-b border-neutral-700 text-left">Nombre</th>
                            <th class="p-3 border-b border-neutral-700 text-left">Email</th>
                            <th class="p-3 border-b border-neutral-700 text-left">Estado</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios">
                        <!-- Se llenará dinámicamente con JS -->
                    </tbody>
                </table>
            </div>
        </section>


        <!-- Panel de Asistencia -->
        <section class="bg-neutral-900 p-8 rounded-xl shadow-lg">
            <h2 class="text-2xl font-bold mb-4 txt-asist">Asistencia</h2>

            <!-- Mensajes dinámicos -->
            <div id="mensaje-asistencia-exito" class="hidden mb-4 p-3 rounded bg-green-700 text-green-100"></div>
            <div id="mensaje-asistencia-error" class="hidden mb-4 p-3 rounded bg-red-700 text-red-100"></div>

            <form id="form-asistencia" class="space-y-4">
                <div>
                    <label class="block mb-1">Email del usuario</label>
                    <input type="email" name="email" class="input-email w-full px-4 py-2 rounded bg-neutral-800 text-neutral-200" required>
                </div>
                <div class="flex gap-4">
                    <button id="btnEntrada" type="button" class="bg-neutral-800 px-5 py-2 rounded-lg hover:bg-neutral-700 transition">
                        Registrar Entrada
                    </button>
                    <button id="btnSalida" type="button" class="bg-neutral-800 px-5 py-2 rounded-lg hover:bg-neutral-700 transition">
                        Registrar Salida
                    </button>
                </div>
            </form>
            <!-- Historial de asistencias -->
            <h3 class="text-xl font-semibold mt-8 mb-4">Historial reciente</h3>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-neutral-800">
                        <th class="p-3 border-b border-neutral-700">Usuario</th>
                        <th class="p-3 border-b border-neutral-700">Email</th>
                        <th class="p-3 border-b border-neutral-700">Tipo</th>
                        <th class="p-3 border-b border-neutral-700">Hora</th>
                    </tr>
                </thead>
                <tbody id="tabla-asistencias"></tbody>
            </table>
        </section>


    </main>

</body>
</html>
