<?php

namespace Database\Seeders;

use App\Models\Directorio;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DirectorioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('directorios')->insert([
            [
                'nombre' => 'Jose Garcia',
                'direccion' => 'Calle 1',
                'telefono' => '12345678',
                'foto' => null
            ],
            [
                'nombre' => 'Juan Pérez',
                'direccion' => 'Calle 2',
                'telefono' => '87654321',
                'foto' => null
            ]
        ]);
    }
}
