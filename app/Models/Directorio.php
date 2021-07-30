<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directorio extends Model
{
    use HasFactory;

    protected $table = 'directorios';

    protected $fillable = ['nombre', 'direccion', 'telefono', 'foto'];

    protected $hidden = ['created_at', 'updated_at'];
}
