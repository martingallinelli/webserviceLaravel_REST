<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Libro;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class LibroController extends Controller
{
    // GET /libros
    public function index()
    {
        try {
            // obtener todos los libros
            $libros = Libro::all();
            // retornar los libros en formato JSON
            return response()->json($libros, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'No hay libros!'], 404);
        }
    }

    // GET /libros/{id}
    public function show($id)
    {
        try {
            // obtener el libro
            $libro = Libro::findOrFail($id);
            // retornar el libro en formato JSON
            return response()->json($libro, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Libro no encontrado!'], 404);
        }
    }

    // POST /libros
    public function store(Request $request)
    {
        // si nos estan enviando un archivo
        if ($request->hasFile('imagen')) {
            // crear un nuevo libro
            $libro = new Libro();

            // obtener el nombre del archivo
            $nombreArchivoOriginal = $request->file('imagen')->getClientOriginalName();
            // asignar un nuevo nombre para guardar la imagen
            // Carbon::now()->timestamp = devuelve el tiempo con formato numerico
            $nuevoNombre = Carbon::now()->timestamp . '_' . $nombreArchivoOriginal;
            // carpeta destino
            $carpetaDestino = './upload/';
            // mover imagen al servidor
            $request->file('imagen')->move($carpetaDestino, $nuevoNombre);

            // capturar el titulo
            $libro->titulo = $request->titulo;
            // capturar el nombre de la imagen
            // ltrim($carpetaDestino, '.') = eliminar el punto de la ruta
            $libro->imagen = ltrim($carpetaDestino, '.') . $nuevoNombre;

            // guardar el libro
            $libro->save();

            // retornamos la informacion del nuevo libro
            return response()->json('Libro guardado!', 200);
        }

        return response()->json(['error' => 'No se ha subido ningun archivo'], 400);
    }

    // PUT /libros/{id}
    public function update(Request $request, $id)
    {
        try {
            // obtener el libro
            $libro = Libro::findOrFail($id);

            // si el titulo recibido es igual al almacenado
            if ($request->input('titulo'))
            {
                // capturar el titulo
                $libro->titulo = $request->titulo;
            }

            // si nos estan enviando un archivo
            if ($request->hasFile('imagen')) {
                // eliminar imagen anterior del servidor
                // ruta en el servidor del archivo
                $rutaArchivo = base_path('public') . $libro->imagen;
                // si existe la ruta del archivo
                if (file_exists($rutaArchivo))
                {
                    // eliminar archivo del servidor
                    unlink($rutaArchivo);
                }

                // actualizar imagen en el servidor
                // obtener el nombre del archivo
                $nombreArchivoOriginal = $request->file('imagen')->getClientOriginalName();
                // asignar un nuevo nombre para guardar la imagen
                // Carbon::now()->timestamp = devuelve el tiempo con formato numerico
                $nuevoNombre = Carbon::now()->timestamp . '_' . $nombreArchivoOriginal;
                // carpeta destino
                $carpetaDestino = './upload/';
                // mover imagen al servidor
                $request->file('imagen')->move($carpetaDestino, $nuevoNombre);
                
                // capturar el nombre de la imagen
                // ltrim($carpetaDestino, '.') = eliminar el punto de la ruta
                $libro->imagen = ltrim($carpetaDestino, '.') . $nuevoNombre;
            }

            // actualizar libro
            $libro->save();
            // retornar el libro en formato JSON
            return response()->json('Libro actualizado!', 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Libro no encontrado!'], 404);
        }
    }

    // DELETE /libros/{id}
    public function delete($id)
    {
        try {
            // obtener el libro
            $libro = Libro::findOrFail($id);
            
            // si existe el libro
            if ($libro)
            {
                // ruta en el servidor del archivo
                $rutaArchivo = base_path('public') . $libro->imagen;

                // si existe la ruta del archivo
                if (file_exists($rutaArchivo))
                {
                    // eliminar archivo del servidor
                    unlink($rutaArchivo);
                }

                // eliminar el libro
                $libro->delete();
                // retornar el libro en formato JSON
                return response()->json('Libro eliminado!', 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Libro no encontrado!'], 404);
        }
    }
}
