<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function index()
    {
        $modulos = Module::with('teachers')->latest()->get();
        return response()->json($modulos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'codigo' => 'required|unique:modules,codigo',
        ]);

        $modulo = Module::create($request->only('nombre', 'codigo'));

        if ($request->filled('teacher_id')) {
            $modulo->teachers()->attach($request->teacher_id);
        }

        return response()->json($modulo, 201);
    }

    public function show(int $id)
    {
        $modulo = Module::with('teachers')->findOrFail($id);
        return response()->json($modulo);
    }

    public function update(Request $request, int $id)
    {
        $modulo = Module::findOrFail($id);

        $request->validate([
            'nombre' => 'required',
            'codigo' => 'required|unique:modules,codigo,' . $id,
        ]);

        $modulo->update($request->only('nombre', 'codigo'));

        // Si no se elige profesor, se quita el que tenía
        $modulo->teachers()->sync($request->filled('teacher_id') ? [$request->teacher_id] : []);

        return response()->json($modulo);
    }

    public function destroy(int $id)
    {
        Module::findOrFail($id)->delete();
        return response()->json(['message' => 'Módulo eliminado']);
    }
}
