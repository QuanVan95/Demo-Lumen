<?php

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

//Plan Routes
$router->group(['prefix' => '/plans', 'middleware' => 'authorization'], function () use ($router) {
    $router->get('/', 'PlanController@getAllPlans');
    $router->get('/{id}', 'PlanController@getPlanById');
    $router->post('/', 'PlanController@createPlan');
    $router->put('/{id}', 'PlanController@updatePlan');
    $router->delete('/{id}', 'PlanController@deletePlan');
    $router->post('/queue', 'PlanController@createPlanWithQueue');
});