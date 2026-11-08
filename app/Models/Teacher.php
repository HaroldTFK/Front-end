<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $table = 'teachers';
    protected $fillable = [
        'nombre',
        'apellidos',
        'telefono',
        'direccion',
        'identificacion',
    ];
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_teacher', 'teacher_id', 'module_id');
    }
}
