<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $table = 'students';
    protected $fillable = [
        'nombre',
        'apellidos',
        'expediente',
        'fecha_nac',
        'foto',
        'foto_public_id',
        'module_id',
    ];
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
