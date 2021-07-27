<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * !    LISTAR TODOS LOS USUARIOS
     */ 
    public function getAll(Request $request)
    {
        if ($request->isJson()) 
        {
            // capturar todos los usuarios
            $users = User::all();
            // responder en formato JSON(mensaje, statusCode)
            return response()->json($users, 200);
        } 

        return response()->json(['Error' => 'Unauthorized!'], 401);
    }

    /**
     * !    LISTAR UN USUARIO
     */
    public function getUser(Request $request, $id)
    {
        if ($request->isJson()) {

            try {
                // capturar usuario
                $user = User::findOrFail($id);
                // responder en formato JSON(mensaje, statusCode)
                return response()->json($user, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['Error' => 'No content!'], 404);
            }
        }

        return response()->json(['error' => 'Unauthorized!'], 401);
    }

    /**
     * !    CREAR UN USUARIO
     */
    public function createUser(Request $request)
    {
        if ($request->isJson()) 
        {
            // capturar todos los datos recibidos
            $data = $request->json()->all();

            // insertar el usuario en la DB
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'api_token' => Str::random(60)
            ]);

            // responder en formato JSON(mensaje, statusCode)
            return response()->json($user, 201);
        } 

        return response()->json(['Error' => 'Unauthorized'], 401);
    }

    /**
     * !    ACTUALIZAR UN USUARIO
     */
    public function updateUser(Request $request, $id)
    {
        if ($request->isJson()) {

            try {
                // capturar el usuario
                $user = User::findOrFail($id);
                // capturar todos los datos recibidos
                $data = $request->json()->all();
                // actualizar el usuario
                User::where('id', $id)->update($data);
                // responder en formato JSON(mensaje, statusCode)
                return response()->json($user, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content!'], 404);
            }
        }

        return response()->json(['error' => 'Unauthorized!'], 401, []);
    }

    /**
     * !    ELIMINAR UN USUARIO
     */
    public function deleteUser(Request $request, $id)
    {
        if ($request->isJson()) {

            try {
                // capturar el usuario
                $user = User::findOrFail($id);
                // eliminar el usuario
                $user->delete();
                // responder en formato JSON(mensaje, statusCode)
                return response()->json($user, 200);
            } catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'No content!'], 404);
            }
        } 
        return response()->json(['error' => 'Unauthorized!'], 401, []);
    }

    /**
     * !    OBTENER TOKEN USUARIO
     */
    public function getToken(Request $request)
    {
        if ($request->isJson())
        {
            try {
                // capturo todos los datos
                $data = $request->json()->all();
                // buscar usuario
                $user = User::where('name', $data['name'])->first();
                // si hay un usuario, y si las contraseñas coinciden
                if ($user && Hash::check($data['password'], $user->password))
                {
                    // responder en formato JSON(mensaje, statusCode)
                    return response()->json($user, 200);
                }

                // return response()->json(['Error' => 'No content!'], 404);
            } catch (ModelNotFoundException $e) {
                return response()->json(['Error' => 'No content!'], 404);                
            }
        } 

        return response()->json(['Error' => 'Unauthorized!'], 401);
    }
}
