<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RutinaController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\ExerciseController;
// Necesario para la excepción en línea
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken; 

Route::get('/', function () {
    return view('welcome');
});



// ==========================================
// GRUPO API (SIN MIDDLEWARE WEB)
// ==========================================
// ... (El resto de tu código sigue igual) ...
Route::prefix('api')->withoutMiddleware('web')->group(function () {
    // ... tus rutas de api ...
     Route::get('/usuarios', [UserController::class, 'index']);
    Route::post('/usuarios', [UserController::class, 'store']); // Antes era 'register', ahora apunta a 'store'
    Route::get('/usuarios/{id}', [UserController::class, 'show']);
    Route::put('/usuarios/{id}', [UserController::class, 'update']);
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);

    // Nota: Si deseas mantener login/logout, debes agregar esos métodos a tu UserController
    // Route::post('/login', [UserController::class, 'login']);
    // Route::post('/logout', [UserController::class, 'logout']);

    // --- HISTORIAL DE RUTINAS ---
    Route::get('/historials', [HistorialController::class, 'index']);
    Route::get('/historials/{id}', [HistorialController::class, 'show']);
    Route::post('/historials', [HistorialController::class, 'store']);
    Route::put('/historials/{id}', [HistorialController::class, 'update']);
    Route::delete('/historials/{id}', [HistorialController::class, 'destroy']);
    Route::get('/historials/user/{user_id}', [HistorialController::class, 'byUser']);

    // --- EJERCICIOS (ExerciseController) ---
    // Listar todos los ejercicios
    Route::get('/exercises', [ExerciseController::class, 'index']);
    
    // Ver un ejercicio específico
    Route::get('/exercises/{id}', [ExerciseController::class, 'show']);
    
    // Importación masiva de ejercicios (Para usar con el JSON)
    Route::post('/exercises/import', [ExerciseController::class, 'import']);
    
    // Eliminar ejercicio
    Route::delete('/exercises/{id}', [ExerciseController::class, 'destroy']);




    // ==========================================
// RUTAS DE RUTINAS
// ==========================================

// IMPORTANTE: Esta ruta debe ir ANTES de las rutas con {id}
// SOLUCIÓN AQUÍ: Le quitamos el middleware CSRF solo a esta ruta
Route::post('/rutinas/importar-masivo', [RutinaController::class, 'importarMasivo'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('/rutinas', [RutinaController::class, 'index']);
Route::get('/rutinas/{id}', [RutinaController::class, 'show']);

// A estas también deberías quitarle el middleware si vas a probarlas con Postman
Route::post('/rutinas', [RutinaController::class, 'store'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::put('/rutinas/{id}', [RutinaController::class, 'update'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::delete('/rutinas/{id}', [RutinaController::class, 'destroy'])
    ->withoutMiddleware([VerifyCsrfToken::class]);


// ... (Resto de tus rutas GET que no necesitan cambios) ...
Route::get('/rutinas/hoy', [RutinaController::class, 'hoy']);
Route::get('/rutinas/dia/{dia}', [RutinaController::class, 'porDia']);

// Rutas POST/DELETE adicionales (también necesitan la excepción para Postman)
Route::post('/rutinas/{rutina_id}/ejercicios', [RutinaController::class, 'addEjercicio'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::post('/rutinas/dia/{rutina_dia_id}/ejercicios', [RutinaController::class, 'addEjercicioADia'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::delete('/rutinas/dia/{rutina_dia_id}/ejercicios/{ejercicio_id}', [RutinaController::class, 'removeEjercicioDeDia'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::post('/rutinas/{rutina_id}/dias', [RutinaController::class, 'storeDia'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::put('/rutinas/dia/{rutina_dia_id}', [RutinaController::class, 'updateDia'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

Route::delete('/rutinas/dia/{rutina_dia_id}', [RutinaController::class, 'destroyDia'])
    ->withoutMiddleware([VerifyCsrfToken::class]);

});