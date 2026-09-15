<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asistencia;
use App\Models\User;

class AsistenciaController extends Controller
{
    /**
     * Registrar entrada del usuario autenticado.
     */
    public function registrarEntrada(Request $request)
    {
        Asistencia::create([
            'user_id' => auth()->id(),
            'tipo' => 'entrada',
        ]);

        return response()->json(['mensaje' => 'Entrada registrada correctamente']);
    }

    /**
     * Registrar salida del usuario autenticado.
     */
    public function registrarSalida(Request $request)
    {
        Asistencia::create([
            'user_id' => auth()->id(),
            'tipo' => 'salida',
        ]);

        return response()->json(['mensaje' => 'Salida registrada correctamente']);
    }

    /**
     * Reporte de entradas atrasadas (después de las 9:30).
     */
    public function reporteAtrasos()
    {
        $atrasos = Asistencia::atrasos()->with('user')->get();
        return response()->json($atrasos);
    }

    /**
     * Reporte de salidas anticipadas (antes de las 17:30).
     */
    public function reporteSalidasAnticipadas()
    {
        $salidas = Asistencia::salidasAnticipadas()->with('user')->get();
        return response()->json($salidas);
    }

    /**
     * Reporte de inasistencias (usuarios sin entrada/salida en un día).
     */
    public function reporteInasistencias()
    {
        $usuarios = User::all();
        $fechaHoy = now()->toDateString();

        $inasistentes = $usuarios->filter(function ($usuario) use ($fechaHoy) {
            return !Asistencia::delDia($fechaHoy, $usuario->id)->exists();
        });

        return response()->json($inasistentes);
    }
}
