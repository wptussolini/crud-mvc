<?php

$route->setNamespace('App\Controllers');
$route->get('/', 'UsersController@index');
$route->get('/create', 'UsersController@create');
$route->get('/show/{id}', 'UsersController@show');
$route->get('/edit/{id}', 'UsersController@edit');
$route->post('/store', 'UsersController@store');
$route->post('/update/{id}', 'UsersController@update');
$route->get('/delete/{id}', 'UsersController@delete');