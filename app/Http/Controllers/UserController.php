<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    // Registrar usuario - USANDO 'nombre'
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:255', 
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'numero_usuario' => 'required|unique:users',
                'edad' => 'nullable|integer',
                'sexo' => 'nullable|in:M,F,Otro',
                'peso' => 'nullable|numeric',
                'altura' => 'nullable|numeric',
                'nivel_conocimiento' => 'nullable|in:Principiante,Intermedio,Avanzado',
                'objetivo' => 'nullable|in:Perder peso,Ganar músculo,Tonificación',
                'tipo_usuario' => 'nullable|in:Registrado,Invitado,Admin'
            ]);

            $userData = [
                'nombre' => $validated['nombre'], // ← Cambiado a 'nombre'
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'numero_usuario' => $validated['numero_usuario']
            ];

            // Agregar campos opcionales si están presentes
            $optionalFields = ['edad', 'sexo', 'peso', 'altura', 'nivel_conocimiento', 'objetivo', 'tipo_usuario'];
            foreach ($optionalFields as $field) {
                if (isset($validated[$field])) {
                    $userData[$field] = $validated[$field];
                }
            }

            $user = User::create($userData);
            $token = JWTAuth::fromUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'user' => $user,
                'token' => $token
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar usuario: ' . $e->getMessage()
            ], 500);
        }
    }

    // Login usuario
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('numero_usuario', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Credenciales inválidas'
                ], 401);
            }

            $user = JWTAuth::user();

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user
            ]);

        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'error' => 'No se pudo crear el token'
            ], 500);
        }
    }

    // Logout usuario
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente'
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al cerrar sesión'
            ], 500);
        }
    }

    // Listar usuarios
    public function index()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }

    // Mostrar usuario específico
    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    // Actualizar usuario
    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuario no encontrado'
                ], 404);
            }

            $validated = $request->validate([
                'nombre' => 'sometimes|string|max:255', // ← Cambiado a 'nombre'
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'numero_usuario' => 'sometimes|unique:users,numero_usuario,' . $id,
                'edad' => 'nullable|integer',
                'sexo' => 'nullable|in:M,F,Otro',
                'peso' => 'nullable|numeric',
                'altura' => 'nullable|numeric',
                'nivel_conocimiento' => 'nullable|in:Principiante,Intermedio,Avanzado',
                'objetivo' => 'nullable|in:Perder peso,Ganar músculo,Tonificación',
                'tipo_usuario' => 'nullable|in:Registrado,Invitado,Admin'
            ]);

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado exitosamente',
                'user' => $user
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Usuario no encontrado'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ]);
    }
}