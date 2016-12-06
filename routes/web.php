<?php

Route::group(['namespace' => '\Ipunkt\Laravel\OAuthIntrospection\Http\Controllers'], function ($router) {
    $router->post('/oauth/introspect', 'IntrospectionController@introspectToken');
});