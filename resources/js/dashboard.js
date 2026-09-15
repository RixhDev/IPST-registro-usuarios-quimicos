async function filtrarUsuarios(estado = 'todos') {
    const response = await fetch(`/api/usuarios?estado=${estado}`, {
        headers: { 'Accept': 'application/json' }
    });
    const tbody = document.querySelector('#tabla-usuarios');
    tbody.innerHTML = '';

    if (!response.ok) {
        throw new Error(`No se pudieron cargar los usuarios (${response.status}).`);
    }

    const data = await response.json();
    (data.usuarios || []).forEach(u => {
        tbody.innerHTML += `
            <tr class="hover:bg-neutral-800 transition">
                <td class="p-3 border-b border-neutral-700">${u.name}</td>
                <td class="p-3 border-b border-neutral-700">${u.email}</td>
                <td class="p-3 border-b border-neutral-700">${u.status || 'sin estado'}</td>
            </tr>
        `;
    });
}

function headersConCsrf() {
    return {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    };
}

async function registrarEntrada() {
    await fetch('/api/asistencia/entrada', { method: 'POST' });
    const mensajeExito = document.getElementById('mensaje-exito');
    mensajeExito.textContent = 'Entrada registrada correctamente.';
    mensajeExito.classList.remove('hidden');
}

async function registrarSalida() {
    await fetch('/api/asistencia/salida', { method: 'POST' });
    const mensajeExito = document.getElementById('mensaje-exito');
    mensajeExito.textContent = 'Salida registrada correctamente.';
    mensajeExito.classList.remove('hidden');
}

//logica de inicialización al cargar
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-usuario');
    const mensajeExito = document.getElementById('mensaje-exito');
    const mensajeError = document.getElementById('mensaje-error');

    filtrarUsuarios('todos').catch(error => {
        mensajeError.textContent = error.message;
        mensajeError.classList.remove('hidden');
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        try {
            const response = await fetch('/api/usuarios', {
                method: 'POST',
                headers: headersConCsrf(),
                body: JSON.stringify(data)
            });

            if (response.ok) {
                const json = await response.json();
                mensajeExito.textContent = `Usuario "${json.usuario.name}" añadido correctamente.`;
                mensajeExito.classList.remove('hidden');
                mensajeError.classList.add('hidden');
                form.reset();
                await filtrarUsuarios('todos'); // refresca la tabla
            } else {
                const error = await response.json();
                mensajeError.textContent = error.message || 'Error al añadir usuario.';
                mensajeError.classList.remove('hidden');
                mensajeExito.classList.add('hidden');
            }
        } catch (err) {
            mensajeError.textContent = 'Error de conexión con el servidor.';
            mensajeError.classList.remove('hidden');
            mensajeExito.classList.add('hidden');
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('btnEntrada').addEventListener('click', () => registrarAsistencia('entrada'));
    document.getElementById('btnSalida').addEventListener('click', () => registrarAsistencia('salida'));

    cargarHistorialAsistencias().catch(error => {
        const mensajeError = document.getElementById('mensaje-asistencia-error');
        mensajeError.textContent = error.message;
        mensajeError.classList.remove('hidden');
    });
});

async function registrarAsistencia(tipo) {
    const form = document.getElementById('form-asistencia');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    data.tipo = tipo;

    const mensajeExito = document.getElementById('mensaje-asistencia-exito');
    const mensajeError = document.getElementById('mensaje-asistencia-error');

    try {
        const response = await fetch('/api/asistencia', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            const registro = await response.json();
            mensajeExito.textContent = registro.mensaje + ` Usuario: ${registro.user.name} (${registro.user.email}) HORA ${registro.hora}.`;
            mensajeExito.classList.remove('hidden');
            mensajeError.classList.add('hidden');
            form.reset();
            cargarHistorialAsistencias(); //refresh historial
        } else {
            const error = await response.json();
            mensajeError.textContent = error.message || '[!] Error al registrar asistencia';
            mensajeError.classList.remove('hidden');
            mensajeExito.classList.add('hidden');
        }
    } catch (err) {
        mensajeError.textContent = '[!] Error de conexión con el servidor';
        mensajeError.classList.remove('hidden');
        mensajeExito.classList.add('hidden');
    }
}

async function cargarHistorialAsistencias() {
    const response = await fetch('/api/asistencias', { headers: { 'Accept': 'application/json' } });
    if (!response.ok) {
        throw new Error(`No se pudo cargar el historial (${response.status}).`);
    }

    const data = await response.json();
    const tbody = document.getElementById('tabla-asistencias');
    tbody.innerHTML = '';

    data.forEach(a => {
        tbody.innerHTML += `
            <tr class="hover:bg-neutral-800 transition">
                <td class="p-3 border-b border-neutral-700">${a.user.name}</td>
                <td class="p-3 border-b border-neutral-700">${a.user.email}</td>
                <td class="p-3 border-b border-neutral-700">${a.tipo}</td>
                <td class="p-3 border-b border-neutral-700">${a.hora}</td>
            </tr>
        `;
    });
}
