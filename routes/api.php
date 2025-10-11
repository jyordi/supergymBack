<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/usuarios', [UserController::class, 'index']);
Route::get('/usuarios/{id}', [UserController::class, 'show']);
Route::post('/usuarios', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
Route::put('/usuarios/{id}', [UserController::class, 'update']);
Route::delete('/usuarios/{id}', [UserController::class, 'destroy']);