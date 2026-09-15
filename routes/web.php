<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

//BLOQUE LOGIN || BLOQUE LOGIN || BLOQUE LOGIN
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//panel de asistencia protegido
Route::middleware('auth')->group(function (){
    Route::get('/asistencia', function (){
        return view('asistencia.index');
    })->name('asistencia');
});

Route::middleware('auth')->get('/welcome', function () {
    return view('welcome');
})->name('welcome');
