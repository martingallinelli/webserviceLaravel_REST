<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // capturar los datos recibidos
        $datos = $request->all();

        // encriptar el password
        $datos['password'] = Hash::make($datos['password']);

        // guardar el usuario
        User::create($datos);
        // retornar respuesta en formato JSON
        return response()->json(['mensaje' => 'Usuario creado correctamente'], 201);
    }

    public function login(Request $request)
    {
        // capturar los datos recibidos
        $datos = $request->all();
        // buscar el usuario
        $usuario = User::where('email', $datos['email'])->first();
        // validar si el usuario existe
        if ($usuario) 
        {
            // validar contraseña
            if (Hash::check($datos['password'], $usuario->password))
            {
                /* // crear api token
                $usuario->api_token = Str::random(100);
                // guardar el usuario
                $usuario->save(); */

                // crear laravel passport token
                $token = $usuario->createToken('contactos')->accessToken;

                // retornar respuesta en formato JSON
                return response()->json([
                    'mensaje' => 'Bienvenido ' . $usuario->name,
                    'token' => $token], 200);
            }             
        }

        // si no se pudo loguear
        // retornar respuesta en formato JSON
        return response()->json(['mensaje' => 'Cuenta o contraseña incorrecta!'], 401);
    }

    public function logout()
    {
        // capturar el usuario logueado
        $usuario = auth()->user();
        /* // eliminar el token de acceso
        $usuario->api_token = null; */
        // eliminar todos los laravel passport token
        $usuario->tokens->each(function($token){
            $token->delete();
        });
        // actualizar el usuario
        $usuario->save();
        // retornar respuesta en formato JSON
        return response()->json(['mensaje' => 'Adios ' . $usuario->name], 200);
    }
}
