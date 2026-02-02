<?php

namespace App\Http\Controllers;

use App\Models\Rutina;
use App\Models\RutinaDia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RutinaSemanalController extends Controller
{
    /**
     * CREAR: Crea una rutina completa de 7 días para un usuario
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nombre' => 'required|string',
            'nivel' => 'required|string',
            // Esperamos un array 'dias', donde cada dia tiene un array 'ejercicios'
            'dias' => 'array', 
        ]);

        return DB::transaction(function () use ($request) {
            
            // 1. Si la nueva es activa, desactivamos las anteriores del usuario
            if ($request->activa) {
                Rutina::where('user_id', $request->user_id)->update(['activa' => false]);
            }

            // 2. Crear la cabecera de la Rutina
            $rutina = Rutina::create([
                'user_id' => $request->user_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'nivel' => $request->nivel,
                'activa' => $request->boolean('activa', true)
            ]);

            // 3. Procesar los días (Lunes a Domingo o los que envíes)
            if ($request->has('dias')) {
                foreach ($request->dias as $diaData) {
                    
                    // Crear el día (Ej: Lunes)
                    $rutinaDia = $rutina->dias()->create([
                        'dia' => $diaData['dia'], // "Lunes"
                        'nivel' => $rutina->nivel
                    ]);

                    // 4. Asignar ejercicios a ese día
                    if (isset($diaData['ejercicios'])) {
                        foreach ($diaData['ejercicios'] as $ejercicio) {
                            $rutinaDia->ejercicios()->attach($ejercicio['exercise_id'], [
                                'series' => $ejercicio['series'] ?? 4,
                                'repeticiones' => $ejercicio['repeticiones'] ?? '12'
                            ]);
                        }
                    }
                }
            }

            return response()->json(['message' => 'Rutina asignada correctamente', 'rutina' => $rutina->load('dias.ejercicios')], 201);
        });
    }

    /**
     * LEER: Obtener la rutina completa de un usuario
     */
    public function show($userId)
    {
        $user = User::findOrFail($userId);
        
        // Traemos la rutina activa con sus días y ejercicios
        $rutina = $user->rutinaActual()
                       ->with('dias.ejercicios')
                       ->first();

        if (!$rutina) {
            return response()->json(['message' => 'El usuario no tiene rutina activa'], 404);
        }

        return response()->json($rutina);
    }

    /**
     * EDITAR: Modificar datos básicos o cambiar ejercicios de un día específico
     */
    public function update(Request $request, $rutinaId)
    {
        $rutina = Rutina::findOrFail($rutinaId);

        // Actualizar datos básicos
        $rutina->update($request->only(['nombre', 'descripcion', 'nivel', 'activa']));

        // Nota: Editar la estructura completa (días/ejercicios) es complejo.
        // Lo ideal es tener endpoints separados para "agregar ejercicio a dia" 
        // o "borrar ejercicio de dia".
        
        return response()->json(['message' => 'Rutina actualizada', 'rutina' => $rutina]);
    }

    /**
     * ELIMINAR: Borra la rutina y todo su contenido en cascada
     */
    public function destroy($rutinaId)
    {
        $rutina = Rutina::findOrFail($rutinaId);
        $rutina->delete(); // Gracias al onDelete('cascade') en la migración, borra días y relaciones.

        return response()->json(['message' => 'Rutina eliminada correctamente']);
    }
}