<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\UserProgress; // Asegúrate de tener este modelo para el historial de progreso
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'usuarios' => User::all()
        ]);
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        // --- LÓGICA PARA FOTOS DE ANTES Y DESPUÉS ---
        // Buscamos registros que tengan foto (no nulos)
        $progreso = UserProgress::where('user_id', $id)
                        ->whereNotNull('foto_path')
                        ->orderBy('created_at', 'asc')
                        ->get();

        $fotoAntes = null;
        $fotoDespues = null;

        if ($progreso->count() > 0) {
            $fotoAntes = $progreso->first()->foto_path;
            $fotoDespues = $progreso->last()->foto_path;
        }
        // ---------------------------------------------

        return response()->json([
            'success' => true,
            'usuario' => $user,
            'progreso' => [
                'antes' => $fotoAntes,
                'despues' => $fotoDespues
            ]
        ]);
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Actualizar datos del usuario y registrar progreso
     */
    public function actualizarDatos(Request $request, $id)
    {
        try {
            // 1. Validar
            $request->validate([
                'peso' => 'required',
                'altura' => 'required',
                // 'edad' => 'nullable|integer', // Puedes agregar validación si quieres
            ]);

            // 2. Buscar Usuario
            $user = User::findOrFail($id);

            // 3. Actualizar Usuario
            $user->peso = $request->peso;
            $user->altura = $request->altura;

            // --- AGREGAR ESTO ---
            if($request->has('edad')) {
                $user->edad = $request->edad;
            }
            // --------------------

            if($request->has('nivel_conocimiento')) {
                $user->nivel_conocimiento = $request->nivel_conocimiento;
            }
            
            $user->save(); // <--- Aquí se guardan los cambios en la tabla 'users'

            // 4. Crear Progreso (Historial)
            $progreso = new UserProgress();
            $progreso->user_id = $user->id;
            $progreso->peso = $request->peso;
            $progreso->altura = $request->altura;
            
            if($request->has('cintura')) $progreso->cintura = $request->cintura;

            // 5. Guardar Foto
            if ($request->hasFile('foto')) {
                $path = $request->file('foto')->store('progress', 'public');
                $progreso->foto_path = url('storage/' . $path);
            }

            $progreso->save();

            return response()->json(['success' => true, 'message' => 'Actualizado correctamente']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ERROR DE LARAVEL: ' . $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Actualizar avatar (foto de perfil) del usuario
     */
   public function actualizarAvatar(Request $request, $id)
{
    // 1. Validación (Subimos a 10MB por si acaso)
    $request->validate([
        'avatar' => 'required|image|max:10240' 
    ]);

    $user = User::findOrFail($id);

    if ($request->hasFile('avatar')) {
        // A. Eliminar avatar anterior si existe (opcional, para no llenar el disco)
        // if ($user->avatar) {
        //     // Lógica para borrar archivo anterior...
        // }

        // B. Guardar la imagen física en: storage/app/public/avatars
        $path = $request->file('avatar')->store('avatars', 'public');

        // C. Generar la URL completa (http://localhost:8000/storage/avatars/...)
        $urlCompleta = url('storage/' . $path);

        // D. ¡AQUÍ SE GUARDA EN LA TABLA USERS!
        $user->avatar = $urlCompleta;
        $user->save(); // <--- Guardado en BD confirmado
    }

    return response()->json([
        'success' => true,
        'message' => 'Avatar actualizado correctamente',
        'avatar_url' => $user->avatar // Devolvemos la URL para que Ionic la muestre al instante
    ]);
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    /**
     * Paso 1: Solicitar recuperación de contraseña
     * Genera un token y simula el envío (o lo envía si tienes mail configurado)
     */
    public function forgotPassword(Request $request)
    {
        // 1. Validar que el email exista
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'El correo electrónico no existe en nuestros registros.',
                'errors' => $validator->errors()
            ], 404);
        }

        // 2. Generar token y fecha
        $token = Str::random(60); // O usa rand(100000, 999999) para un código numérico
        $email = $request->email;

        // 3. Guardar en la tabla 'password_reset_tokens' (Tabla por defecto de Laravel)
        // Nota: Asegúrate de borrar tokens viejos de este email primero
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $token, // Si usas Hash, recuerda hashearlo, pero para tokens simples se suele guardar directo o hasheado dependiendo de tu config
            'created_at' => Carbon::now()
        ]);

        // 4. Enviar Email (Aquí simulamos el envío para que puedas probar en Postman)
        /* TODO: Configurar Mailtrap o SMTP en .env y descomentar esto:
           Mail::send('emails.password_reset', ['token' => $token], function($message) use ($email){
               $message->to($email);
               $message->subject('Recuperación de contraseña');
           });
        */

        return response()->json([
            'success' => true,
            'message' => 'Se ha generado el token de recuperación.',
            // OJO: En producción NO devuelvas el token aquí, esto es solo para que pruebes sin enviar emails.
            'debug_token' => $token 
        ], 200);
    }

    /**
     * Paso 2: Restablecer la contraseña usando el token
     */
    public function resetPassword(Request $request)
    {
        // 1. Validar datos
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed', // 'confirmed' exige que envíes un campo 'password_confirmation'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 2. Verificar si el token es válido en la base de datos
        $passwordReset = DB::table('password_reset_tokens')
                            ->where('email', $request->email)
                            ->where('token', $request->token)
                            ->first();

        if (!$passwordReset) {
            return response()->json([
                'success' => false,
                'message' => 'El token es inválido o el email es incorrecto.'
            ], 400);
        }

        // 3. Verificar expiración del token (Ejemplo: 60 minutos de validez)
        if (Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'El token ha expirado. Solicita uno nuevo.'
            ], 400);
        }

        // 4. Cambiar la contraseña del usuario
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // 5. Borrar el token usado para que no se pueda reutilizar
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña restablecida exitosamente. Ya puedes iniciar sesión.'
        ], 200);
    }
    /**
     * Registro de usuario nuevo
     */
    public function register(Request $request)
    {
        // 1. Validar datos
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users', // Esto evita el error de email duplicado
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 2. Generar numero_usuario si no viene (Usa timestamp + random)
        $numUsuario = $request->numero_usuario ?? 'USER-' . time() . rand(100,999);

        // 3. Crear usuario
        try {
            $user = User::create([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'numero_usuario' => $numUsuario, // <--- AQUÍ LA CORRECCIÓN
                'nivel_conocimiento' => $request->nivel_conocimiento ?? 'Principiante',
                'peso' => $request->peso,
                'altura' => $request->altura,
                'tipo_usuario' => 'Registrado'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            // Esto te ayudará a ver el error real si pasa otra cosa
            return response()->json([
                'success' => false,
                'message' => 'Error en base de datos',
                'error' => $e->getMessage()
            ], 500);
        }
    }


  










    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        // 1. Validar
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // 2. Buscar usuario por email
        $user = User::where('email', $request->email)->first();

        // 3. Verificar contraseña
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // 4. Login exitoso (Retornamos el usuario para guardar en local)
        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'user' => $user,
            // Si usaras JWT, aquí devolverías el 'token'
        ]);
    }



    
   

    

    /**
     * Logout de usuario
     */
    public function logout(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ], 200);
    }
}
