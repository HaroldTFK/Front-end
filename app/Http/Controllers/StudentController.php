<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Lista todos los estudiantes con su módulo
    public function index()
    {
        $estudiantes = Student::with('module:id,nombre')->latest()->get();
        return response()->json($estudiantes);
    }

    // Crea un estudiante (con foto opcional)
    public function store(Request $request)
    {
        $request->validate([
            'nombre'     => 'required',
            'apellidos'  => 'required',
            'expediente' => 'required|unique:students,expediente',
            'fecha_nac'  => 'required|date',
            'module_id'  => 'required|exists:modules,id',
            'foto'       => 'nullable|image|max:4096',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $foto = $this->subirFoto($request->file('foto'));
            $data['foto'] = $foto['secure_url'];
            $data['foto_public_id'] = $foto['public_id'];
        }

        $estudiante = Student::create($data);
        return response()->json($estudiante, 201);
    }

    // Muestra un estudiante
    public function show(int $id)
    {
        $estudiante = Student::with('module:id,nombre')->findOrFail($id);
        return response()->json($estudiante);
    }

    // Actualiza un estudiante (y cambia la foto si llega una nueva)
    public function update(Request $request, int $id)
    {
        $estudiante = Student::findOrFail($id);

        $request->validate([
            'nombre'     => 'required',
            'apellidos'  => 'required',
            'expediente' => 'required|unique:students,expediente,' . $id,
            'fecha_nac'  => 'required|date',
            'module_id'  => 'required|exists:modules,id',
            'foto'       => 'nullable|image|max:4096',
        ]);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            // Borra la foto anterior de Cloudinary
            if ($estudiante->foto_public_id) {
                $this->cloudinary()->uploadApi()->destroy($estudiante->foto_public_id);
            }

            $foto = $this->subirFoto($request->file('foto'));
            $data['foto'] = $foto['secure_url'];
            $data['foto_public_id'] = $foto['public_id'];
        }

        $estudiante->update($data);
        return response()->json($estudiante);
    }

    // Elimina un estudiante y su foto
    public function destroy(int $id)
    {
        $estudiante = Student::findOrFail($id);

        if ($estudiante->foto_public_id) {
            $this->cloudinary()->uploadApi()->destroy($estudiante->foto_public_id);
        }

        $estudiante->delete();
        return response()->json(['message' => 'Estudiante eliminado']);
    }

    // ---- Funciones de ayuda ----

    private function cloudinary()
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => config('services.cloudinary.cloud_name'),
                'api_key'    => config('services.cloudinary.api_key'),
                'api_secret' => config('services.cloudinary.api_secret'),
            ],
            'url' => ['secure' => true],
        ]);
    }

    private function subirFoto($archivo)
    {
        return $this->cloudinary()->uploadApi()->upload($archivo->getRealPath(), [
            'folder' => 'estudiantes',
        ]);
    }
}
