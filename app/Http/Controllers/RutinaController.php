<?php

namespace App\Http\Controllers;

use App\Models\Rutina;
use App\Models\Ejercicio;
use Illuminate\Http\Request;

class RutinaController extends Controller
{
    public function index()
    {
        return response()->json(Rutina::with('ejercicios')->get());
    }

    public function show($id)
    {
        $rutina = Rutina::with('ejercicios')->find($id);
        if (!$rutina) {
            return response()->json(['error' => 'Rutina no encontrada'], 404);
        }
        return response()->json($rutina);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string'
        ]);
        $rutina = Rutina::create($data);
        return response()->json($rutina, 201);
    }

    public function update(Request $request, $id)
    {
        $rutina = Rutina::find($id);
        if (!$rutina) {
            return response()->json(['error' => 'Rutina no encontrada'], 404);
        }
        $data = $request->validate([
            'nombre' => 'sometimes|string',
            'descripcion' => 'nullable|string'
        ]);
        $rutina->update($data);
        return response()->json($rutina);
    }

    public function destroy($id)
    {
        $rutina = Rutina::find($id);
        if (!$rutina) {
            return response()->json(['error' => 'Rutina no encontrada'], 404);
        }
        $rutina->delete();
        return response()->json(['message' => 'Rutina eliminada']);
    }

    // Asignar ejercicios a una rutina
    public function addEjercicio(Request $request, $rutina_id)
    {
        $rutina = Rutina::find($rutina_id);
        if (!$rutina) {
            return response()->json(['error' => 'Rutina no encontrada'], 404);
        }
        $data = $request->validate([
            'ejercicio_id' => 'required|exists:ejercicios,id',
            'series' => 'required|integer',
            'repeticiones' => 'required|string'
        ]);
        $rutina->ejercicios()->attach($data['ejercicio_id'], [
            'series' => $data['series'],
            'repeticiones' => $data['repeticiones']
        ]);
        return response()->json(['message' => 'Ejercicio agregado a la rutina']);
    }

    // Eliminar ejercicio de una rutina
    public function removeEjercicio($rutina_id, $ejercicio_id)
    {
        $rutina = Rutina::find($rutina_id);
        if (!$rutina) {
            return response()->json(['error' => 'Rutina no encontrada'], 404);
        }
        $rutina->ejercicios()->detach($ejercicio_id);
        return response()->json(['message' => 'Ejercicio eliminado de la rutina']);
    }
}
