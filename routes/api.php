<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificacionController;

Route::get('/usuarios', [UserController::class, 'index']);
Route::get('/usuarios/{id}', [UserController::class, 'show']);
Route::post('/usuarios', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
Route::put('/usuarios/{id}', [UserController::class, 'update']);
Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);

// Rutas protegidas por autenticación
Route::middleware(['auth:api'])->group(function () {
    // Notificaciones
    // GET  /api/notificaciones                  -> listar todas
    // GET  /api/notificaciones/{id}             -> ver notificación
    // POST /api/notificaciones                  -> crear { user_id, mensaje, fecha_envio? }
    // PUT  /api/notificaciones/{id}/read        -> marcar como leída
    // DELETE /api/notificaciones/{id}           -> eliminar
    // GET  /api/notificaciones/user/{user_id}   -> listar por usuario

    Route::get('/notificaciones', [NotificacionController::class, 'index']);
    Route::get('/notificaciones/{id}', [NotificacionController::class, 'show']);
    Route::post('/notificaciones', [NotificacionController::class, 'store']);
    Route::put('/notificaciones/{id}/read', [NotificacionController::class, 'markAsRead']);
    Route::delete('/notificaciones/{id}', [NotificacionController::class, 'destroy']);
    Route::get('/notificaciones/user/{user_id}', [NotificacionController::class, 'byUser']);
});