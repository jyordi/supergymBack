<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HistorialController extends Controller
{
    public function index() 
    { 
        // Ordenar por fecha_realizacion para que la lista salga bien
        return response()->json(Historial::with(['user'])->orderBy('fecha_realizacion', 'desc')->get()); 
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'user_id' => 'required|exists:users,id',
            'rutina_nombre' => 'required|string',
            'nivel' => 'required|string',
            'duration_seconds' => 'required|integer',
            'calories' => 'required|integer',
            'difficulty' => 'required|string',
            'fecha_realizacion' => 'nullable|date', // Recibe YYYY-MM-DD
            'completada' => 'sometimes|boolean'
        ]);

        // TRUCO: Si no se envía fecha, usar hoy.
        // Si se envía fecha (desde Angular), usar esa.
        $fecha = isset($data['fecha_realizacion']) 
                 ? Carbon::parse($data['fecha_realizacion']) 
                 : Carbon::now();

        // Asignamos la fecha personalizada
        $data['fecha_realizacion'] = $fecha->format('Y-m-d');

        // *** FIX CRÍTICO ***
        // Forzamos 'created_at' para que sea igual a la fecha del usuario.
        // Esto arregla las gráficas que leen created_at por defecto.
        $historial = new Historial($data);
        $historial->created_at = $fecha; 
        $historial->updated_at = $fecha;
        $historial->save();

        return response()->json($historial->load(['user']), 201);
    }

    // Método para obtener estadísticas o historial por usuario
    public function byUser($user_id) 
    { 
        return response()->json(
            Historial::where('user_id', $user_id)
                     ->orderBy('fecha_realizacion', 'desc') // Usar la fecha correcta
                     ->get()
        ); 
    }
}