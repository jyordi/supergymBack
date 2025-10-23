<?php

namespace App\Http\Controllers;

use App\Models\Rutina;
use App\Models\RutinaDia;
use App\Models\Ejercicio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RutinaController extends Controller
{
    // Obtener todas las rutinas
    public function index()
    {
        return response()->json(Rutina::with('ejercicios')->get());
    }

    // Obtener rutina por id
    public function show($id)
    {
        $rutina = Rutina::with('ejercicios')->find($id);
        if (!$rutina) return response()->json(['error'=>'Rutina no encontrada'], 404);
        return response()->json($rutina);
    }

    // Crear rutina
    public function store(Request $req)
    {
        $data = $req->validate([
            'nombre'=>'required|string',
            'descripcion'=>'nullable|string',
            'dia'=>'nullable|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'nivel'=>'nullable|in:Principiante,Intermedio,Avanzado'
        ]);
        $rutina = Rutina::create($data);
        return response()->json($rutina, 201);
    }

    // Actualizar rutina
    public function update(Request $req, $id)
    {
        $rutina = Rutina::find($id);
        if (!$rutina) return response()->json(['error'=>'Rutina no encontrada'], 404);
        $data = $req->validate([
            'nombre'=>'sometimes|string',
            'descripcion'=>'nullable|string',
            'dia'=>'sometimes|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'nivel'=>'sometimes|in:Principiante,Intermedio,Avanzado'
        ]);
        $rutina->update($data);
        return response()->json($rutina);
    }

    // Eliminar rutina
    public function destroy($id)
    {
        $rutina = Rutina::find($id);
        if (!$rutina) return response()->json(['error'=>'Rutina no encontrada'], 404);
        $rutina->delete();
        return response()->json(['message'=>'Rutina eliminada']);
    }

    // Obtener rutinas del día actual (o pasar ?dia=Martes)
    public function hoy(Request $req)
    {
        $dia = $req->query('dia');
        if (!$dia) {
            $days = [
                1 => 'Lunes',2 => 'Martes',3 => 'Miércoles',4 => 'Jueves',
                5 => 'Viernes',6 => 'Sábado',7 => 'Domingo'
            ];
            $dia = $days[Carbon::now()->dayOfWeekIso];
        }
        $query = RutinaDia::with('ejercicios')->where('dia', $dia);
        if ($req->has('nivel')) {
            $query->where('nivel', $req->query('nivel'));
        }
        $result = $query->get();
        return response()->json($result);
    }

    // Obtener rutinas por día (opcional nivel)
    public function porDia(Request $req, $dia)
    {
        $dia = ucfirst(mb_strtolower($dia));
        $query = RutinaDia::with('ejercicios')->where('dia', $dia);
        if ($req->has('nivel')) {
            $query->where('nivel', $req->query('nivel'));
        }
        return response()->json($query->get());
    }

    // Asignar ejercicio con series/repeticiones a rutina (tabla rutina_ejercicio)
    public function addEjercicio(Request $request, $rutina_id)
    {
        $rutina = Rutina::find($rutina_id);
        if (!$rutina) return response()->json(['error'=>'Rutina no encontrada'], 404);
        $data = $request->validate([
            'ejercicio_id'=>'required|exists:ejercicios,id',
            'series'=>'required|integer',
            'repeticiones'=>'required|string',
            'nivel'=>'nullable|in:Principiante,Intermedio,Avanzado'
        ]);
        $pivot = ['series'=>$data['series'],'repeticiones'=>$data['repeticiones']];
        if (isset($data['nivel'])) $pivot['nivel'] = $data['nivel'];
        $rutina->ejercicios()->attach($data['ejercicio_id'], $pivot);
        return response()->json(['message'=>'Ejercicio agregado a la rutina']);
    }

    // Agregar ejercicio a una rutina en un día concreto (tabla rutina_dia_ejercicio)
    public function addEjercicioADia(Request $request, $rutina_dia_id)
    {
        $data = $request->validate([
            'ejercicio_id' => 'required|exists:ejercicios,id',
            'series' => 'required|integer',
            'repeticiones' => 'required|string'
        ]);
        $rutinaDia = RutinaDia::find($rutina_dia_id);
        if (!$rutinaDia) return response()->json(['error'=>'RutinaDia no encontrada'], 404);
        $rutinaDia->ejercicios()->attach($data['ejercicio_id'], [
            'series' => $data['series'],
            'repeticiones' => $data['repeticiones']
        ]);
        return response()->json(['message'=>'Ejercicio agregado al día de la rutina']);
    }

    // Eliminar ejercicio de una rutina en un día
    public function removeEjercicioDeDia($rutina_dia_id, $ejercicio_id)
    {
        $rutinaDia = RutinaDia::find($rutina_dia_id);
        if (!$rutinaDia) return response()->json(['error'=>'RutinaDia no encontrada'], 404);
        $rutinaDia->ejercicios()->detach($ejercicio_id);
        return response()->json(['message'=>'Ejercicio removido del día de la rutina']);
    }

    // Crear una entrada RutinaDia para una rutina (asignar día y nivel a la rutina)
    public function storeDia(Request $req, $rutina_id)
    {
        $data = $req->validate([
            'dia' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'nivel' => 'nullable|in:Principiante,Intermedio,Avanzado'
        ]);

        $rutina = Rutina::find($rutina_id);
        if (!$rutina) {
            return response()->json(['error' => 'Rutina no encontrada'], 404);
        }

        $rutinaDia = RutinaDia::create([
            'rutina_id' => $rutina_id,
            'dia' => $data['dia'],
            'nivel' => $data['nivel'] ?? null
        ]);

        return response()->json($rutinaDia, 201);
    }

    // Actualizar una entrada RutinaDia
    public function updateDia(Request $req, $rutina_dia_id)
    {
        $rutinaDia = RutinaDia::find($rutina_dia_id);
        if (!$rutinaDia) {
            return response()->json(['error' => 'RutinaDia no encontrada'], 404);
        }

        $data = $req->validate([
            'dia' => 'sometimes|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'nivel' => 'sometimes|in:Principiante,Intermedio,Avanzado'
        ]);

        $rutinaDia->update($data);
        return response()->json($rutinaDia);
    }

    // Eliminar una entrada RutinaDia
    public function destroyDia($rutina_dia_id)
    {
        $rutinaDia = RutinaDia::find($rutina_dia_id);
        if (!$rutinaDia) {
            return response()->json(['error' => 'RutinaDia no encontrada'], 404);
        }
        $rutinaDia->delete();
        return response()->json(['message' => 'RutinaDia eliminada']);
    }
}
