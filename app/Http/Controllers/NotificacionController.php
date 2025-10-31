<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificacionController extends Controller
{
    // Listar todas (opcional paginación)
    public function index()
    {
        return response()->json(Notificacion::with('user')->latest()->get());
    }

    // Mostrar una notificación
    public function show($id)
    {
        $n = Notificacion::with('user')->find($id);
        if (!$n) return response()->json(['error' => 'Notificación no encontrada'], 404);
        return response()->json($n);
    }

    // Crear notificación
    public function store(Request $req)
    {
        $data = $req->validate([
            'user_id' => 'required|exists:users,id',
            'mensaje' => 'required|string',
            'fecha_envio' => 'nullable|date',
            'leida' => 'sometimes|boolean',
        ]);

        if (empty($data['fecha_envio'])) {
            $data['fecha_envio'] = Carbon::now();
        }

        $n = Notificacion::create($data);
        return response()->json($n->load('user'), 201);
    }

    // Marcar como leída
    public function markAsRead($id)
    {
        $n = Notificacion::find($id);
        if (!$n) return response()->json(['error' => 'Notificación no encontrada'], 404);
        $n->leida = true;
        $n->save();
        return response()->json($n);
    }

    // Eliminar notificación
    public function destroy($id)
    {
        $n = Notificacion::find($id);
        if (!$n) return response()->json(['error' => 'Notificación no encontrada'], 404);
        $n->delete();
        return response()->json(['message' => 'Notificación eliminada']);
    }

    // Listar notificaciones de un usuario
    public function byUser($user_id)
    {
        $list = Notificacion::where('user_id', $user_id)->with('user')->orderByDesc('fecha_envio')->get();
        return response()->json($list);
    }
}
