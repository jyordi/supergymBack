<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    // Registro
    public function register(Request $req)
    {
        $data = $req->validate([
            'numero_usuario' => 'required|unique:users',
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'edad' => 'nullable|integer',
            'sexo' => 'nullable',
            'peso' => 'nullable|numeric',
            'altura' => 'nullable|numeric',
            'nivel_conocimiento' => 'required',
            'objetivo' => 'nullable',
            'tipo_usuario' => 'required'
        ]);
        
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        
        // Generar token JWT
        $token = JWTAuth::fromUser($user);
        
        return response()->json(['user' => $user, 'token' => $token], 201);
    }

    // Login
    public function login(Request $req)
    {
        $credentials = $req->only('numero_usuario', 'password');
        
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Credenciales inválidas'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'No se pudo crear el token'], 500);
        }
        
        $user = JWTAuth::user(); // Solución: obtener el usuario desde JWTAuth

        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
    }

    // Logout
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Sesión cerrada']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Error al cerrar sesión'], 500);
        }
    }

    // Perfil actual
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    }

    // Actualizar perfil
    public function update(Request $req)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            $data = $req->validate([
                'name' => 'sometimes|string',
                'email' => 'sometimes|email|unique:users,email,' . $user->id,
                'edad' => 'sometimes|integer',
                'sexo' => 'sometimes',
                'peso' => 'sometimes|numeric',
                'altura' => 'sometimes|numeric',
                'nivel_conocimiento' => 'sometimes',
                'objetivo' => 'sometimes',
                'tipo_usuario' => 'sometimes',
                'password' => 'sometimes|min:6'
            ]);
            
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            
            $user->update($data);
            return response()->json($user);
            
        } catch (JWTException $e) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    }

    // Recuperar contraseña
    public function sendResetLink(Request $req)
    {
        $req->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($req->only('email'));
        
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Link de recuperación enviado'])
            : response()->json(['error' => 'No se pudo enviar el link'], 500);
    }

    // Restablecer contraseña
    public function resetPassword(Request $req)
    {
        $data = $req->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed'
        ]);
        
        $status = Password::reset($data, function($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        });
        
        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Contraseña restablecida'])
            : response()->json(['error' => 'Error al restablecer'], 500);
    }
}