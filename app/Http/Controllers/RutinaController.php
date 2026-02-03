<?php

namespace App\Http\Controllers;

use App\Models\Rutina;
use App\Models\RutinaDia;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RutinaController extends Controller
{
    /**
     * Listar todas las rutinas con sus relaciones
     */
    public function index()
    {
        return Rutina::with(['exercises', 'dias.exercises'])->get();
    }

    /**
     * Crear una nueva rutina básica
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'dia' => 'nullable|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'nivel' => 'required|in:Principiante,Intermedio,Avanzado'
        ]);

        $rutina = Rutina::create($data);
        return response()->json($rutina, 201);
    }

    /**
     * Mostrar una rutina específica
     */
    public function show($id)
    {
        $rutina = Rutina::with(['exercises', 'dias.exercises'])->findOrFail($id);
        return response()->json($rutina);
    }

    /**
     * Actualizar rutina
     */
    public function update(Request $request, $id)
    {
        $rutina = Rutina::findOrFail($id);
        $rutina->update($request->all());
        return response()->json($rutina);
    }

    /**
     * Eliminar rutina
     */
    public function destroy($id)
    {
        Rutina::destroy($id);
        return response()->json(['message' => 'Rutina eliminada']);
    }

    // ==========================================
    // LÓGICA DE FECHAS Y FILTROS
    // ==========================================

    /**
     * Obtener rutinas del día actual
     */
    public function hoy()
    {
        $mapaDias = [
            'Monday'    => 'Lunes',
            'Tuesday'   => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday'  => 'Jueves',
            'Friday'    => 'Viernes',
            'Saturday'  => 'Sábado',
            'Sunday'    => 'Domingo',
        ];

        $diaIngles = Carbon::now()->format('l');
        $diaEspanol = $mapaDias[$diaIngles];

        $rutinas = Rutina::where('dia', $diaEspanol)
                    ->orWhereHas('dias', function($q) use ($diaEspanol) {
                        $q->where('dia', $diaEspanol);
                    })
                    ->with(['dias' => function($q) use ($diaEspanol) {
                        $q->where('dia', $diaEspanol)->with('exercises');
                    }, 'exercises'])
                    ->get();

        return response()->json(['dia' => $diaEspanol, 'rutinas' => $rutinas]);
    }

    /**
     * Obtener rutinas por nombre de día específico
     */
    public function porDia($dia)
    {
        $rutinas = Rutina::where('dia', $dia)
                    ->orWhereHas('dias', function($q) use ($dia) {
                        $q->where('dia', $dia);
                    })
                    ->with(['dias' => function($q) use ($dia) {
                        $q->where('dia', $dia)->with('exercises');
                    }])
                    ->get();

        return response()->json($rutinas);
    }

    /**
     * NUEVO MÉTODO: Buscar rutinas por Nivel Y Día específico
     * Ejemplo uso: /rutinas/buscar/Intermedio/Lunes
     */
    

    // ==========================================
    // GESTIÓN DE EJERCICIOS Y DÍAS
    // ==========================================

    /**
     * Asignar ejercicio directamente a la rutina
     */
    public function addEjercicio(Request $request, $rutina_id)
    {
        $request->validate([
            'ejercicio_id' => 'required|exists:exercises,id',
            'series' => 'integer',
            'repeticiones' => 'string',
            'nivel' => 'in:Principiante,Intermedio,Avanzado'
        ]);

        $rutina = Rutina::findOrFail($rutina_id);
        
        $rutina->exercises()->attach($request->ejercicio_id, [
            'series' => $request->series ?? 3,
            'repeticiones' => $request->repeticiones ?? '12',
            'nivel' => $request->nivel ?? null
        ]);

        return response()->json(['message' => 'Ejercicio agregado a la rutina']);
    }

    /**
     * Crear un sub-día para una rutina
     */
    public function storeDia(Request $request, $rutina_id)
    {
        $request->validate([
            'dia' => 'required|in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',
            'nivel' => 'nullable|in:Principiante,Intermedio,Avanzado'
        ]);

        $rutinaDia = RutinaDia::create([
            'rutina_id' => $rutina_id,
            'dia' => $request->dia,
            'nivel' => $request->nivel
        ]);

        return response()->json($rutinaDia, 201);
    }


    public function buscarPorNivelYDia($nivel, $dia)
{
    // Normalizar
    $nivel = ucfirst(strtolower($nivel));
    $dia = ucfirst(strtolower($dia));

    $nivelesValidos = ['Principiante', 'Intermedio', 'Avanzado'];
    $diasValidos = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];

    if (!in_array($nivel, $nivelesValidos) || !in_array($dia, $diasValidos)) {
        return response()->json(['error' => 'Nivel o día no válido'], 400);
    }

    $rutinas = Rutina::where('nivel', $nivel)
        ->whereHas('dias', function ($q) use ($dia, $nivel) {
            $q->where('dia', $dia)
              ->where('nivel', $nivel);
        })
        ->with([
            'dias' => function ($q) use ($dia, $nivel) {
                $q->where('dia', $dia)
                  ->where('nivel', $nivel)
                  ->with([
                      'exercises.images',        // ← AÑADIDO
                      'exercises.instructions',  // ← AÑADIDO
                      'exercises'                // base
                  ]);
            }
        ])
        ->get();

    return response()->json($rutinas);
}


    
    /**
     * Asignar ejercicio a un sub-día
     */
    public function addEjercicioADia(Request $request, $rutina_dia_id)
    {
        $request->validate([
            'ejercicio_id' => 'required|exists:exercises,id',
            'series' => 'integer',
            'repeticiones' => 'string'
        ]);

        $rutinaDia = RutinaDia::findOrFail($rutina_dia_id);
        
        $rutinaDia->exercises()->attach($request->ejercicio_id, [
            'series' => $request->series ?? 3,
            'repeticiones' => $request->repeticiones ?? '12'
        ]);

        return response()->json(['message' => 'Ejercicio agregado al día de rutina']);
    }

    /**
     * Eliminar ejercicio de un día concreto
     */
    public function removeEjercicioDeDia($rutina_dia_id, $ejercicio_id)
    {
        $rutinaDia = RutinaDia::findOrFail($rutina_dia_id);
        $rutinaDia->exercises()->detach($ejercicio_id);
        
        return response()->json(['message' => 'Ejercicio removido del día']);
    }

    public function updateDia(Request $request, $rutina_dia_id) {
        $dia = RutinaDia::findOrFail($rutina_dia_id);
        $dia->update($request->all());
        return response()->json($dia);
    }

    public function destroyDia($rutina_dia_id) {
        RutinaDia::destroy($rutina_dia_id);
        return response()->json(['message' => 'Día de rutina eliminado']);
    }

    // ==========================================
    // IMPORTACIÓN MASIVA
    // ==========================================

    public function importarMasivo(Request $request)
    {
        $data = $request->validate([
            '*.nombre' => 'required|string',
            '*.nivel' => 'required|string',
            '*.dias' => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();
            
            $count = 0;
            foreach ($data as $item) {
                $rutina = Rutina::create([
                    'nombre' => $item['nombre'],
                    'descripcion' => $item['descripcion'] ?? null,
                    'dia' => $item['dia'] ?? null,
                    'nivel' => ucfirst(strtolower($item['nivel']))
                ]);

                if (isset($item['dias']) && is_array($item['dias'])) {
                    foreach ($item['dias'] as $diaData) {
                        $rutinaDia = RutinaDia::create([
                            'rutina_id' => $rutina->id,
                            'dia' => $diaData['dia'],
                            'nivel' => $diaData['nivel'] ?? null
                        ]);

                        if (isset($diaData['ejercicios']) && is_array($diaData['ejercicios'])) {
                            foreach ($diaData['ejercicios'] as $ejercicioData) {
                                // Buscar por nombre exacto
                               $ejercicio = Exercise::where('name', 'LIKE', $ejercicioData['nombre'])->first();
                                
                                if ($ejercicio) {
                                    $rutinaDia->exercises()->attach($ejercicio->id, [
                                        'series' => $ejercicioData['series'] ?? 3,
                                        'repeticiones' => $ejercicioData['repeticiones'] ?? '12'
                                    ]);
                                }
                            }
                        }
                    }
                }
                $count++;
            }

            DB::commit();
            return response()->json(['message' => "$count rutinas importadas"], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}