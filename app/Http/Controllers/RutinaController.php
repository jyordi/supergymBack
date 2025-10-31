<?php

namespace App\Http\Controllers;

use App\Models\Rutina;
use App\Models\RutinaDia;
use App\Models\Ejercicio;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RutinaController extends Controller
{
    public function index() { return response()->json(Rutina::with('ejercicios')->get()); }

    public function show($id)
    {
        $r = Rutina::with('ejercicios')->find($id);
        if (!$r) return response()->json(['error'=>'Rutina no encontrada'], 404);
        return response()->json($r);
    }

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

    public function destroy($id)
    {
        $rutina = Rutina::find($id);
        if (!$rutina) return response()->json(['error'=>'Rutina no encontrada'], 404);
        $rutina->delete();
        return response()->json(['message'=>'Rutina eliminada']);
    }

    // Obtener rutinas del día actual
    public function hoy(Request $req)
    {
        $days = [1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado',7=>'Domingo'];
        $dia = $req->query('dia') ?? $days[Carbon::now()->dayOfWeekIso];
        $query = RutinaDia::with('ejercicios')->where('dia', ucfirst(mb_strtolower($dia)));
        if ($req->has('nivel')) $query->where('nivel', $req->query('nivel'));
        return response()->json($query->get());
    }

    // Asignar ejercicio a rutina (pivot rutina_ejercicio)
    public function addEjercicio(Request $req, $rutina_id)
    {
        $rutina = Rutina::find($rutina_id);
        if (!$rutina) return response()->json(['error'=>'Rutina no encontrada'], 404);
        $data = $req->validate([
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

    // Agregar ejercicio a una rutina en un día concreto (rutina_dia_ejercicio)
    public function addEjercicioADia(Request $req, $rutina_dia_id)
    {
        $data = $req->validate([
            'ejercicio_id'=>'required|exists:ejercicios,id',
            'series'=>'required|integer',
            'repeticiones'=>'required|string'
        ]);
        $rd = RutinaDia::find($rutina_dia_id);
        if (!$rd) return response()->json(['error'=>'RutinaDia no encontrada'], 404);
        $rd->ejercicios()->attach($data['ejercicio_id'], ['series'=>$data['series'],'repeticiones'=>$data['repeticiones']]);
        return response()->json(['message'=>'Ejercicio agregado al día de la rutina']);
    }

    // Crear / actualizar / eliminar RutinaDia (si usas estas rutas)
    public function storeDia(Request $req, $rutina_id)
    {
        $data = $req->validate(['dia'=>'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo','nivel'=>'nullable|in:Principiante,Intermedio,Avanzado']);
        $rutina = Rutina::find($rutina_id); if (!$rutina) return response()->json(['error'=>'Rutina no encontrada'],404);
        $rd = RutinaDia::create(['rutina_id'=>$rutina_id,'dia'=>$data['dia'],'nivel'=>$data['nivel'] ?? null]);
        return response()->json($rd,201);
    }

    public function updateDia(Request $req, $rutina_dia_id)
    {
        $rd = RutinaDia::find($rutina_dia_id); if (!$rd) return response()->json(['error'=>'RutinaDia no encontrada'],404);
        $data = $req->validate(['dia'=>'sometimes|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo','nivel'=>'sometimes|in:Principiante,Intermedio,Avanzado']);
        $rd->update($data); return response()->json($rd);
    }

    public function destroyDia($rutina_dia_id)
    {
        $rd = RutinaDia::find($rutina_dia_id); if (!$rd) return response()->json(['error'=>'RutinaDia no encontrada'],404);
        $rd->delete(); return response()->json(['message'=>'RutinaDia eliminada']);
    }
}
