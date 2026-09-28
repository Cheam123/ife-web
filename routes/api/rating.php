<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth']], function () {
    Route::post('/update', 'API\V1\CommonController@set_rating')->name('rating.update');
});
