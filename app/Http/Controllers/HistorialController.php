<?php

namespace App\Http\Controllers;

use App\Models\Historial;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function index() { return response()->json(Historial::with(['user','rutina'])->get()); }

    public function show($id)
    {
        $h = Historial::with(['user','rutina'])->find($id);
        if (!$h) return response()->json(['error'=>'Registro no encontrado'],404);
        return response()->json($h);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'user_id'=>'required|exists:users,id',
            'rutina_id'=>'required|exists:rutinas,id',
            'fecha_realizacion'=>'nullable|date',
            'completada'=>'sometimes|boolean'
        ]);
        $h = Historial::create($data);
        return response()->json($h->load(['user','rutina']),201);
    }

    public function update(Request $req, $id)
    {
        $h = Historial::find($id); if (!$h) return response()->json(['error'=>'Registro no encontrado'],404);
        $data = $req->validate(['fecha_realizacion'=>'nullable|date','completada'=>'sometimes|boolean']);
        $h->update($data); return response()->json($h->load(['user','rutina']));
    }

    public function destroy($id)
    {
        $h = Historial::find($id); if (!$h) return response()->json(['error'=>'Registro no encontrado'],404);
        $h->delete(); return response()->json(['message'=>'Registro eliminado']);
    }

    public function byUser($user_id) { return response()->json(Historial::with('rutina')->where('user_id',$user_id)->get()); }
}
