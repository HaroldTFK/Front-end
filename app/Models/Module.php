<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $table = 'modules';
    protected $fillable = [
        'nombre',
        'codigo',
    ];
    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'module_teacher', 'module_id', 'teacher_id');
    }
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
