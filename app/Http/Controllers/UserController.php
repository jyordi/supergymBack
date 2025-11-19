<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;



class UserController extends Controller
{
    // Registrar
    public function register(Request $req)
    {
        $data = $req->validate([
            'nombre' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'numero_usuario' => 'required|unique:users'
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
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

        $user = JWTAuth::user();
        return response()->json(['token' => $token, 'user' => $user]);
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

    // Listar usuarios
    public function index()
    {
        return response()->json(User::all());
    }

    // Mostrar usuario
    public function show($id)
    {
        $u = User::find($id);
        if (!$u) return response()->json(['error' => 'Usuario no encontrado'], 404);
        return response()->json($u);
    }

    // Actualizar usuario
    public function update(Request $req, $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['error' => 'Usuario no encontrado'], 404);

        $data = $req->validate([
            'nombre' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|min:6|confirmed',
            'numero_usuario' => 'sometimes|unique:users,numero_usuario,'.$user->id
        ]);

        if (isset($data['password'])) $data['password'] = Hash::make($data['password']);
        $user->update($data);
        return response()->json($user);
    }

    // Eliminar usuario
    public function destroy($id)
    {
        $u = User::find($id);
        if (!$u) return response()->json(['error' => 'Usuario no encontrado'], 404);
        $u->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }
}