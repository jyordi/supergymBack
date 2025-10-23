<?php

namespace App\Http\Controllers;

use App\Models\Rutina;
use App\Models\RutinaDia;
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
            // traducir día actual a formato con primera mayúscula en español
            $dia = ucfirst(mb_strtolower(Carbon::now()->locale('es')->translatedFormat('l')));
        }
        $query = RutinaDia::with('ejercicios.rutinas')->where('dia', $dia);
        if ($req->has('nivel')) {
            $query->where('nivel', $req->query('nivel'));
        }
        $result = $query->get();
        return response()->json($result);
    }

    // Asignar ejercicio con series/repeticiones a rutina
    public function addEjercicio(Request $request, $rutina_id)
    {
        $rutina = Rutina::find($rutina_id);
        if (!$rutina) return response()->json(['error'=>'Rutina no encontrada'], 404);
        $data = $request->validate([
            'ejercicio_id'=>'required|exists:ejercicios,id',
            'series'=>'required|integer',
            'repeticiones'=>'required|string'
        ]);
        $rutina->ejercicios()->attach($data['ejercicio_id'], [
            'series'=>$data['series'],
            'repeticiones'=>$data['repeticiones']
        ]);
        return response()->json(['message'=>'Ejercicio agregado']);
    }

    // Agregar ejercicio a una rutina en un día concreto
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
}
