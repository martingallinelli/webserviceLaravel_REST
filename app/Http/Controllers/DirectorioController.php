<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateDirectorioRequest;
use App\Http\Requests\UpdateDirectorioRequest;
use App\Models\Directorio;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DirectorioController extends Controller
{
    /**
     * GET - Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // si existe el parámetro de busqueda (nombre o telefono)
        if ($request->has('search')) {
            // buscamos en el modelo
            $directorios = Directorio::where('nombre', 'like', '%' . $request->search . '%')
                                    ->orWhere('telefono', 'like', '%' . $request->search . '%')
                                    ->get();
        // si no existe el parámetro de busqueda
        } else {
            $directorios = Directorio::all();
        }

        // retornar el/los directorio en formato JSON
        return count($directorios) 
            ? response()->json($directorios, 200) 
            : response()->json(['error' => 'No se encontraron resultados'], 404);
    }

    /**
     * POST - Store a newly created resource in storage.
     */
    public function store(CreateDirectorioRequest $request)
    {
        // crear un nuevo directorio
        $directorio = new Directorio();
        // capturar los datos
        $directorio->nombre = $request->nombre;
        $directorio->direccion = $request->direccion;
        $directorio->telefono = $request->telefono;

        // si nos estan enviando un archivo
        if ($request->hasFile('foto')) {
            // subir archivo al servidor
            $directorio->foto = $this->uploadFile($request->file('foto'));
        }

        // guardar el directorio
        $directorio->save();
        // retornar respuesta en formato JSON
        return response()->json(['mensaje' => 'Directorio creado correctamente'], 201);
    }

    /**
     * Upload a file to the server.
     */ 
    private function uploadFile($file)
    {
        // obtener el nombre del archivo
        $nombreArchivoOriginal = $file->getClientOriginalName();
        // asignar un nuevo nombre para guardar la imagen
        $nuevoNombre = time() . '_' . $nombreArchivoOriginal;
        // carpeta destino
        $carpetaDestino = './upload/';
        // mover imagen al servidor
        $file->move($carpetaDestino, $nuevoNombre);

        // capturar el nombre de la imagen
        // ltrim($carpetaDestino, '.') = eliminar el punto de la ruta
        $nombreArchivo = ltrim($carpetaDestino, '.') . $nuevoNombre;
        // retornar el nombre de la imagen
        return $nombreArchivo;
    }

    /**
     * GET - Display the specified resource.
     */
    public function show($id)
    {
        try {
            // obtener el directorio
            $directorio = Directorio::findOrFail($id);
            // retornar el directorio en formato JSON
            return response()->json($directorio, 200);
        } catch (ModelNotFoundException $e) {
            // retornar el error en formato JSON
            return response()->json(['error' => 'No se encontraron resultados'], 404);
        }
    }

    /**
     * PUT - Update the specified resource in storage.
     */
    public function update(UpdateDirectorioRequest $request, $id)
    {
        try {
            // obtener los datos recibidos
            $datos = $request->all();
            // obtener el directorio
            $directorio = Directorio::findOrFail($id);

            // si nos estan enviando un archivo
            if ($request->hasFile('foto')) {
                // eliminar imagen anterior del servidor
                $this->deleteFile($directorio->foto);
                // subir archivo al servidor
                $datos['foto'] = $this->uploadFile($request->file('foto'));
            }

            // actualizar los datos del directorio
            $directorio->update($datos);
            // retornar respuesta en formato JSON
            return response()->json(['mensaje' => 'Directorio actualizado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            // retornar el error en formato JSON
            return response()->json(['error' => 'No se encontraron resultados'], 404);
        }
    }

    /**
     * Remove a file from the server.
     */
    private function deleteFile($file)
    {
        // ruta en el servidor del archivo
        $rutaArchivo = base_path('public') . $file;

        // si existe la ruta del archivo
        if (file_exists($rutaArchivo))
        {
            // eliminar archivo del servidor
            unlink($rutaArchivo);
        }
    }

    /**
     * DELETE - Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // obtener el directorio
            $directorio = Directorio::findOrFail($id);
            // si el nombre del archivo no esta vacio
            if ($directorio->foto != '')
            {
                // eliminar archivo del servidor
                $this->deleteFile($directorio->foto);
            }
            // eliminar el directorio
            $directorio->delete();
            // retornar respuesta en formato JSON
            return response()->json(['mensaje' => 'Directorio eliminado correctamente'], 200);
        } catch (ModelNotFoundException $e) {
            // retornar el error en formato JSON
            return response()->json(['error' => 'No se encontraron resultados'], 404);
        }
    }
}
