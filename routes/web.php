<?php

use Illuminate\Support\Str;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// INDEX
    $router->get('/', function () use ($router) {
        return $router->app->version();
    });

// GENERAR CLAVE (prueba)
    $router->get('/key', function() {
        return Str::random(32);
    });

// AUTENTICACION
    $router->post('/users/login', ['uses' => 'UsersController@getToken']);

// USERS
    // para acceder a estas rutas debes estar autenticado
    $router->group(['middleware' => ['auth']], function() use ($router)
    {
        $router->get('/users', ['uses' => 'UsersController@getAll']);
        $router->get('/users/{id}', ['uses' => 'UsersController@getUser']);
        $router->post('/users', ['uses' => 'UsersController@createUser']);
        $router->put('/users/{id}', ['uses' => 'UsersController@updateUser']);
        $router->delete('/users/{id}', ['uses' => 'UsersController@deleteUser']);
    });