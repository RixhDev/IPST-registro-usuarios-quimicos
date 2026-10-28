<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Creacion de usuario
     */
    public function crear(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'status' => 'nullable|in:activo,inactivo',
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->input('status') ?: 'activo',
        ]);

        return response()->json(['mensaje' => '[+] Usuario creado correctamente.', 'usuario' => $usuario]);
    }

    /**
     * Actualización de usuario existente
     */
    public function modificar(Request $request, $id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json([
                'error' => '[!] Usuario no encontrado.',
                'id' => $id
            ], 404);
        }

        // Blindaje opcional de seguridad: Evitar alterar el correo del admin principal (ID 1)
        if ($usuario->id === 1 && $request->has('email') && $request->email !== $usuario->email) {
            return response()->json([
                'error' => '[!] Acción no permitida: No se puede modificar el correo del administrador principal.'
            ], 403);
        }

        $usuario->update([
            'name' => $request->name ?? $usuario->name,
            'email' => $request->email ?? $usuario->email,
            'password' => $request->password ? Hash::make($request->password) : $usuario->password,
            'status' => $request->status ?? $usuario->status,
        ]);

        return response()->json(['mensaje' => '[+] Usuario modificado correctamente.', 'usuario' => $usuario]);
    }

    /**
     * Borrar usuario
     */
    public function eliminar($id)
    {
        try {
            $usuario = User::findOrFail($id);
            $usuario->delete();

            return response()->json(['mensaje' => '[+] Usuario eliminado correctamente.']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => '[!] Usuario no encontrado.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * Listar alternat. [administración].
     */
    public function listar()
    {
        $usuarios = User::all();
        return response()->json($usuarios);
    }

    public function listarPorEstado(Request $request)
    {
        $estado = $request->query('estado', 'todos');

        // validacion
        if (!in_array($estado, ['activo', 'inactivo', 'todos'])) {
            return response()->json([
                'error' => '[!] Estado inválido -> Usar: (activo, inactivo, todos.)'
            ], 400);
        }

        // consulta segun estado
        $usuarios = $estado === 'todos'
            ? User::all()
            : User::where('status', $estado)->get();

        // COND.B.: sin resultados
        if ($usuarios->isEmpty()) {
            return response()->json([
                'estado' => $estado,
                'total' => 0,
                'usuarios' => [],
                'mensaje' => 'No se encontraron empleados con el estado solicitado.'
            ], 404);
        }

        // flujo usual
        return response()->json([
            'estado' => $estado,
            'total' => $usuarios->count(),
            'usuarios' => $usuarios
        ], 200);
    }
}