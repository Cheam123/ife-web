<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {

    Route::get('/', 'API\V1\UsersController@index')->name('users.index');
    Route::get('/view/{id}', 'API\V1\UsersController@view')->name('users.view');
    Route::get('/create', 'API\V1\UsersController@create')->name('users.create');
    Route::post('/store', 'API\V1\UsersController@store')->name('users.store');
    Route::get('/edit/{id}', 'API\V1\UsersController@edit')->name('users.edit');
    Route::post('/update', 'API\V1\UsersController@update')->name('users.update');
    Route::post('/delete', 'API\V1\UsersController@delete')->name('users.delete');

    Route::get('/profile', 'API\V1\UsersController@profile')->name('users.profile');
    Route::get('/changepassword', 'API\V1\UsersController@change_password')->name('users.changepassword');
    Route::post('/updatepassword', 'API\V1\UsersController@update_password')->name('users.updatepassword');
    Route::post('/resetpassword', 'API\V1\UsersController@reset_password')->name('users.resetpassword');
});
