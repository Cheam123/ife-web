<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {

    Route::get('/', 'API\V1\IfeAreaController@index')->name('area.index');
    Route::post('/store', 'API\V1\IfeAreaController@store')->name('area.store');
    Route::post('/update/{id}', 'API\V1\IfeAreaController@update')->name('area.update');
    Route::post('/delete', 'API\V1\IfeAreaController@delete')->name('area.delete');
});
