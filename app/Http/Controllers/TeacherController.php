<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index()
    {
        $profesores = Teacher::with('modules')->latest()->get();
        return response()->json($profesores);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'         => 'required',
            'apellidos'      => 'required',
            'telefono'       => 'required',
            'direccion'      => 'required',
            'identificacion' => 'required|unique:teachers,identificacion',
        ]);

        $profesor = Teacher::create($request->all());
        $profesor->modules()->sync($request->input('module_id', []));

        return response()->json($profesor, 201);
    }

    public function show(int $id)
    {
        $profesor = Teacher::with('modules')->findOrFail($id);
        return response()->json($profesor);
    }

    public function update(Request $request, int $id)
    {
        $profesor = Teacher::findOrFail($id);

        $request->validate([
            'nombre'         => 'required',
            'apellidos'      => 'required',
            'telefono'       => 'required',
            'direccion'      => 'required',
            'identificacion' => 'required|unique:teachers,identificacion,' . $id,
        ]);

        $profesor->update($request->all());
        $profesor->modules()->sync($request->input('module_id', []));

        return response()->json($profesor);
    }

    public function destroy(int $id)
    {
        Teacher::findOrFail($id)->delete();
        return response()->json(['message' => 'Profesor eliminado']);
    }
}
