<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RutinaController;

Route::get('/', function () {
    return view('welcome');
});

// RUTINAS
Route::get('/rutinas', [RutinaController::class, 'index']);
Route::get('/rutinas/{id}', [RutinaController::class, 'show']);
Route::post('/rutinas', [RutinaController::class, 'store']);
Route::put('/rutinas/{id}', [RutinaController::class, 'update']);
Route::delete('/rutinas/{id}', [RutinaController::class, 'destroy']);

// Obtener rutinas del día actual
Route::get('/rutinas/hoy', [RutinaController::class, 'hoy']);

// Obtener rutinas por día (ej: /rutinas/dia/Martes)
// Opcional: /rutinas/dia/Martes?nivel=Avanzado
Route::get('/rutinas/dia/{dia}', [RutinaController::class, 'porDia']);

// Asignar ejercicio a rutina (pivot rutina_ejercicio)
// POST /rutinas/{rutina_id}/ejercicios
/*
Body JSON:
{
  "ejercicio_id": 5,
  "series": 4,
  "repeticiones": "10-12"
}
*/
Route::post('/rutinas/{rutina_id}/ejercicios', [RutinaController::class, 'addEjercicio']);

// Asignar ejercicio a rutina en día (pivot rutina_dia_ejercicio)
// POST /rutinas/dia/{rutina_dia_id}/ejercicios
/*
Body JSON:
{
  "ejercicio_id": 5,
  "series": 4,
  "repeticiones": "10-12"
}
*/
Route::post('/rutinas/dia/{rutina_dia_id}/ejercicios', [RutinaController::class, 'addEjercicioADia']);

// Eliminar ejercicio de un día concreto
Route::delete('/rutinas/dia/{rutina_dia_id}/ejercicios/{ejercicio_id}', [RutinaController::class, 'removeEjercicioDeDia']);

// Crear entrada día para una rutina
// POST /rutinas/{rutina_id}/dias
// Body JSON: { "dia":"Lunes", "nivel":"Intermedio" }
Route::post('/rutinas/{rutina_id}/dias', [RutinaController::class, 'storeDia']);

// Actualizar entrada día
// PUT /rutinas/dia/{rutina_dia_id}
// Body JSON: { "dia":"Martes", "nivel":"Avanzado" }
Route::put('/rutinas/dia/{rutina_dia_id}', [RutinaController::class, 'updateDia']);

// Eliminar entrada día
// DELETE /rutinas/dia/{rutina_dia_id}
Route::delete('/rutinas/dia/{rutina_dia_id}', [RutinaController::class, 'destroyDia']);

// RUTAS DE USUARIOS (ejemplo existentes)
// Grupo de rutas API sin middleware web
Route::prefix('api')->withoutMiddleware('web')->group(function () {
    Route::get('/usuarios', [UserController::class, 'index']);
    Route::get('/usuarios/{id}', [UserController::class, 'show']);
    Route::post('/usuarios', [UserController::class, 'register']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::put('/usuarios/{id}', [UserController::class, 'update']);
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);
});