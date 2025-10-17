<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RutinaController;

Route::get('/', function () {
    return view('welcome');
});

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

// Rutinas CRUD
Route::get('/rutinas', [RutinaController::class, 'index']);
Route::get('/rutinas/{id}', [RutinaController::class, 'show']);
Route::post('/rutinas', [RutinaController::class, 'store']);
Route::put('/rutinas/{id}', [RutinaController::class, 'update']);
Route::delete('/rutinas/{id}', [RutinaController::class, 'destroy']);

// Asignar ejercicio a rutina
Route::post('/rutinas/{rutina_id}/ejercicios', [RutinaController::class, 'addEjercicio']);

// Eliminar ejercicio de rutina
Route::delete('/rutinas/{rutina_id}/ejercicios/{ejercicio_id}', [RutinaController::class, 'removeEjercicio']);