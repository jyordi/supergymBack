<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\ExerciseImage;
use App\Models\ExerciseInstruction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExerciseController extends Controller
{
    /**
     * Obtener todos los ejercicios con sus relaciones
     */
    public function index()
    {
        return Exercise::with(['instructions', 'images'])->get();
    }

    /**
     * Importar masivamente el JSON de ejercicios
     */
    public function import(Request $request)
    {
        // Validar que sea un array
        $data = $request->validate([
            '*.name' => 'required|string',
            '*.force' => 'nullable|string',
            '*.level' => 'required|string',
            '*.mechanic' => 'nullable|string',
            '*.equipment' => 'nullable|string',
            '*.primaryMuscles' => 'nullable|array', // Nota: CamelCase como viene del JSON
            '*.secondaryMuscles' => 'nullable|array',
            '*.instructions' => 'nullable|array',
            '*.category' => 'nullable|string',
            '*.images' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            foreach ($data as $item) {
                // 1. Mapear nivel 'expert' a 'advanced' para cumplir con el enum de la DB
                $level = strtolower($item['level']);
                if ($level === 'expert' || $level === 'experto') {
                    $level = 'advanced';
                } elseif ($level === 'principiante') {
                    $level = 'beginner';
                } elseif ($level === 'intermedio') {
                    $level = 'intermediate';
                }

                // 2. Mapear fuerza (traducir si viene en español al inglés del enum si es necesario)
                $force = match(strtolower($item['force'] ?? '')) {
                    'empuje', 'push' => 'push',
                    'tirón', 'pull' => 'pull',
                    'estático', 'static' => 'static',
                    default => null
                };

                // 3. Crear el Ejercicio
                $exercise = Exercise::create([
                    'name' => $item['name'],
                    'force' => $force,
                    'level' => $level,
                    'mechanic' => $item['mechanic'] ?? null,
                    'equipment' => $item['equipment'] ?? null,
                    'category' => $item['category'] ?? null,
                    // Mapeamos las claves del JSON (camelCase) a la DB (snake_case)
                    'primary_muscles' => $item['primaryMuscles'] ?? [],
                    'secondary_muscles' => $item['secondaryMuscles'] ?? [],
                ]);

                // 4. Guardar Instrucciones
                if (isset($item['instructions']) && is_array($item['instructions'])) {
                    foreach ($item['instructions'] as $instructionText) {
                        ExerciseInstruction::create([
                            'exercise_id' => $exercise->id,
                            'instruction' => $instructionText
                        ]);
                    }
                }

                // 5. Guardar Imágenes
                if (isset($item['images']) && is_array($item['images'])) {
                    foreach ($item['images'] as $imagePath) {
                        ExerciseImage::create([
                            'exercise_id' => $exercise->id,
                            'image_path' => $imagePath
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['message' => 'Ejercicios importados correctamente', 'count' => count($data)], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al importar: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return Exercise::with(['instructions', 'images'])->findOrFail($id);
    }
    
    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete(); // Eliminará en cascada instrucciones e imágenes por la migración
        return response()->json(['message' => 'Ejercicio eliminado']);
    }
}