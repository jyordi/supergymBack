<?php

use App\Http\Controllers\RutinaSemanalController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RutinaController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\ExerciseController;
// Necesario para la excepción en línea
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken; 
use Illuminate\Support\Facades\Artisan;

Route::get('/', function () {
    return view('welcome');
});



// ==========================================
// GRUPO API (SIN MIDDLEWARE WEB)
// ==========================================
// ... (El resto de tu código sigue igual) ...
Route::prefix('api')->withoutMiddleware('web')->group(function () {
    // ... tus rutas de api ...
    // Agrega estas líneas específicas para Auth
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// Rutas de recuperación
// routes/api.php
Route::post('forgot-password', [UserController::class, 'forgotPassword']);
Route::post('reset-password', [UserController::class, 'resetPassword']);


    Route::get('/users/{id}', [UserController::class, 'show']);
     Route::get('/usuarios', [UserController::class, 'index']);
    Route::post('/usuarios', [UserController::class, 'store']); // Antes era 'register', ahora apunta a 'store'
    Route::get('/usuarios/{id}', [UserController::class, 'show']);
    Route::put('/usuarios/{id}', [UserController::class, 'update']);
    Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);
    
    
    
    Route::post('/users/{id}/actualizar', [UserController::class, 'actualizarDatos']);
    Route::post('/users/{id}/avatar', [UserController::class, 'actualizarAvatar']);

    

    Route::get('/users/{id}/progress-history', [UserController::class, 'getProgressHistory']);
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
    Route::get('/historial/peso/{user_id}', [StatsController::class, 'getWeightHistory']);

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

// Obtener rutinas por nivel y día
Route::get('/rutinas/buscar/{nivel}/{dia}', [RutinaController::class, 'buscarPorNivelYDia']);


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


// Guardar al terminar la rutina
Route::post('/historial', [StatsController::class, 'store']);

// Obtener datos para el dashboard (home)
Route::get('/historial/stats/{user_id}', [StatsController::class, 'getStats']);

Route::get('/historial/peso/{user_id}', [StatsController::class, 'getWeightHistory']);



// Asignar nueva rutina a usuario
Route::post('/rutinas-semanales', [RutinaSemanalController::class, 'store']);

// Ver rutina activa de un usuario (por ID de usuario)
Route::get('/users/{id}/rutina-actual', [RutinaSemanalController::class, 'show']);

// Editar nombre/estado de una rutina
Route::put('/rutinas-semanales/{id}', [RutinaSemanalController::class, 'update']);

// Eliminar rutina
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::delete('/rutinas-semanales/{id}', [RutinaSemanalController::class, 'destroy']);


});

// Ruta para crear el enlace simbólico de storage
Route::get('/run-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return '¡Éxito! Enlace simbólico creado correctamente.';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});



Route::get('/limpiar-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return '¡Caché limpiada con éxito, Guerrero!';
});

Route::get('/crear-link', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return '¡Puente de imágenes creado con éxito!';
});

//hola

