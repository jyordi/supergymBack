<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


// Registro de usuario
Route::post('/register', [AuthController::class, 'register']);

// Login de usuario
Route::post('/login', [AuthController::class, 'login']);

// Logout (requiere token)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

// Obtener perfil del usuario logueado
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:api');

// Actualizar perfil
Route::put('/update', [AuthController::class, 'update'])->middleware('auth:api');

// Recuperar contraseña
Route::post('/password/email', [AuthController::class, 'sendResetLink']);

// Restablecer contraseña
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
