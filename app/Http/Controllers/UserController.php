<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * creacion de usuario
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
     * actualización de usuario existente
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

        $usuario->update([
            'name' => $request->name ?? $usuario->name,
            'email' => $request->email ?? $usuario->email,
            'password' => $request->password ? Hash::make($request->password) : $usuario->password,
        ]);
        return response()->json(['mensaje' => '[+] Usuario modificado correctamente.', 'usuario' => $usuario]);
    }

    /**
     * borrar usuario
     */
    public function eliminar($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();

        return response()->json(['mensaje' => '[+] Usuario eliminado correctamente.']);
    }

    /**
     * listar alternat. [administración]).
     */
    public function listar()
    {
        $usuarios = User::all();
        return response()->json($usuarios);
    }

    public function listarPorEstado(Request $request)
    {
        $estado = $request->query('estado', 'todos');

        //validacion
        if (!in_array($estado, ['activo', 'inactivo', 'todos'])) {
            return response()->json([
                'error' => '[!] Estado inválido -> Usar: (activo, inactivo, todos.)'
            ], 400);
        }

        //consulta segun estado
        $usuarios = $estado === 'todos'
            ? User::all()
            : User::where('status', $estado)->get();

        //COND.B.: sin resultados
        if ($usuarios->isEmpty()) {
            return response()->json([
                'estado' => $estado,
                'total' => 0,
                'usuarios' => [],
                'mensaje' => 'No se encontraron empleados con el estado solicitado.'
            ], 404);
        }

        //flujo usual
        return response()->json([
            'estado' => $estado,
            'total' => $usuarios->count(),
            'usuarios' => $usuarios
        ], 200);
    }


}
