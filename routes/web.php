<?php

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// GET
$router->get('/libros', 'LibroController@index');
$router->get('/libros/{id}', 'LibroController@show');

// POST
$router->post('/libros', 'LibroController@store');
$router->post('/libros/{id}', 'LibroController@update');

// DELETE
$router->delete('/libros/{id}', 'LibroController@delete');
