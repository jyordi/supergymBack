<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Registro
Route::post('/register', [AuthController::class, 'register']);

// Login
Route::post('/login', [AuthController::class, 'login']);

// Logout
Route::post('/logout', [AuthController::class, 'logout']);

// Perfil actual
Route::get('/me', [AuthController::class, 'me']);

// Actualizar perfil
Route::put('/update', [AuthController::class, 'update']);

// Recuperar contraseña
Route::post('/password/email', [AuthController::class, 'sendResetLink']);

// Restablecer contraseña
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
