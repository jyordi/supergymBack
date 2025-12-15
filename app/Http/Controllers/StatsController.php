<?php

namespace App\Http\Controllers;

use App\Models\UserProgress;
use App\Models\WorkoutHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    // Guardar historial de entrenamiento
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'rutina_nombre' => 'required',
            'duration_seconds' => 'required',
            'calories' => 'required',
            'fecha_realizacion' => 'nullable|date'
        ]);

        $fechaFinal = $request->fecha_realizacion ? $request->fecha_realizacion : Carbon::now()->format('Y-m-d');

        $history = WorkoutHistory::create([
            'user_id' => $request->user_id,
            'rutina_nombre' => $request->rutina_nombre,
            'nivel' => $request->nivel,
            'duration_seconds' => $request->duration_seconds,
            'calories' => $request->calories,
            'difficulty' => $request->difficulty,
            'completed_date' => $fechaFinal 
        ]);

        return response()->json(['message' => 'Guardado', 'data' => $history], 201);
    }

    // Obtener Estadísticas Generales (Racha, Totales, Calendario)
    public function getStats($user_id)
    {
        // 1. Calculate Streak
        $streak = 0;
        $fechas = WorkoutHistory::where('user_id', $user_id)
            ->distinct()
            ->orderBy('completed_date', 'desc')
            ->pluck('completed_date')
            ->toArray();

        $hoy = Carbon::now()->format('Y-m-d');
        $ayer = Carbon::yesterday()->format('Y-m-d');

        if (!empty($fechas) && ($fechas[0] == $hoy || $fechas[0] == $ayer)) {
            $checkDate = (in_array($hoy, $fechas)) ? Carbon::now() : Carbon::yesterday();
            foreach ($fechas as $date) {
                if ($date == $checkDate->format('Y-m-d')) {
                    $streak++;
                    $checkDate->subDay();
                } else { break; }
            }
        }

        // 2. Totals
        $totalWorkouts = WorkoutHistory::where('user_id', $user_id)->count();
        $totalCalories = WorkoutHistory::where('user_id', $user_id)->sum('calories');
        $totalMinutes = round(WorkoutHistory::where('user_id', $user_id)->sum('duration_seconds') / 60);

        // 3. Calendar Data
        $calendarData = WorkoutHistory::where('user_id', $user_id)
            ->select('completed_date', DB::raw('count(*) as total'))
            ->groupBy('completed_date')
            ->get();

        // 4. Weekly Chart Data
        $weeklyActivity = [];
        $diasLabels = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $minutes = WorkoutHistory::where('user_id', $user_id)
                ->whereDate('completed_date', $dateStr)
                ->sum('duration_seconds') / 60;

            $weeklyActivity[] = [
                'dia' => $diasLabels[$date->dayOfWeek],
                'full_date' => $dateStr,
                'valor' => round($minutes)
            ];
        }

        return response()->json([
            'streak' => $streak,
            'total_workouts' => $totalWorkouts,
            'total_calories' => $totalCalories,
            'total_minutes' => $totalMinutes,
            'calendar' => $calendarData,
            'weekly_chart' => $weeklyActivity
        ]);
    }

    // New Function for Weight Chart
    public function getWeightHistory($user_id)
    {
        $history = UserProgress::where('user_id', $user_id)
            ->whereNotNull('peso')
            ->select('peso', 'created_at')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'fecha' => Carbon::parse($item->created_at)->format('d M'),
                    'peso' => (float)$item->peso // Ensure float for charts
                ];
            });

        return response()->json($history);
    }
   
}