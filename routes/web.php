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


$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'coupons'], function () use ($router) {
        $router->get('/', ['as' => 'promo-codes', 'uses' => 'PromoCodeController@index']);
        $router->post('create', ['as' => 'promo-code-create', 'uses' => 'PromoCodeController@store']);
        $router->post('validate', ['as' => 'promo-code-validity', 'uses' => 'PromoCodeController@show']);
    });
    $router->group(['prefix' => 'coupons/{code}'], function () use ($router) {
        $router->put('deactivate', ['as' => 'promo-code-deactivate', 'uses' => 'PromoCodeController@deactivate']);
        $router->put('configure/radius', ['as' => 'promo-code-radius-config', 'uses' => 'PromoCodeController@update']);
    });
});


