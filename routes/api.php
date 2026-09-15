<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AsistenciaController;
use App\Models\User;
use App\Models\Asistencia;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//ruta de prueba para verificar que la API funciona
Route::get('/ping', function () {
    return response()->json(['status' => 'ok', 'mensaje' => 'API funcionando']);
});

//gestion de usuarios
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/usuarios', [UserController::class, 'crear']);          // Crear usuario
    Route::put('/usuarios/{id}', [UserController::class, 'modificar']);  // Modificar usuario
    Route::delete('/usuarios/{id}', [UserController::class, 'eliminar']); // Eliminar usuario
    Route::get('/usuarios', [UserController::class, 'listarPorEstado']); // Listar usuarios filtrados
});

//gestion de asistencia
Route::middleware('auth:sanctum')->group(function (){
    Route::post('/asistencia/entrada', [AsistenciaController::class, 'registrarEntrada']);   //registra entrada
    Route::post('/asistencia/salida', [AsistenciaController::class, 'registrarSalida']);     //registra salida

    //endpoint reportes
    Route::get('/reportes/atrasos', [AsistenciaController::class, 'reporteAtrasos']);             //entradas atrasadas
    Route::get('/reportes/salidas', [AsistenciaController::class, 'reporteSalidasAnticipadas']); //salidas anticipadas
    Route::get('/reportes/inasistencias', [AsistenciaController::class, 'reporteInasistencias']); //inasistencias
});

Route::get('/asistencias', function (){
    return Asistencia::with('user')
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get()
        ->map(function ($a) {
            return [
                'user' => [
                    'name' => $a->user->name,
                    'email' => $a->user->email,
                ],
                'tipo' => $a->tipo,
                'hora' => $a->created_at->format('Y-m-d H:i:s'),
            ];
        });
});

Route::post('/asistencia', function (Request $request){
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => '[!] El usuario no existe'], 404);
    }

    // Valida tipo [entrada / salida]
    if (!in_array($request->tipo, ['entrada','salida'])) {
        return response()->json(['message' => '[!] Tipo de asistencia inválido'], 422);
    }

    $registro = Asistencia::create([
        'user_id' => $user->id,
        'tipo' => $request->tipo,
    ]);

    return response()->json([
        'user' => $user,
        'tipo' => $registro->tipo,
        'hora' => $registro->created_at->format('Y-m-d H:i:s'),
        'mensaje' => $registro->tipo === 'entrada'
            ? 'Entrada registrada correctamente.'
            : 'Salida registrada correctamente.'
    ]);
    return \App\Models\Asistencia::with('user')
        ->orderBy('created_at', 'desc')
        ->take(10) //almacena últimos 10 registros
        ->get()
        ->map(function ($a) {
            return [
                'user' => $a->user,
                'tipo' => $a->tipo,
                'hora' => $a->created_at->format('Y-m-d H:i:s'),
            ];
        });
});
