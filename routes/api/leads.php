<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth']], function () {

    Route::get('/','API\V1\LeadController@index')->name('lead.index');
    
    Route::get('/view/{id}', 'API\V1\LeadController@view')->name('lead.view');
    
    Route::get('/edit/{id}', 'API\V1\LeadController@edit')->name('lead.edit');
    Route::post('/update', 'API\V1\LeadController@update')->name('lead.update');
    
    Route::get('/create', 'API\V1\LeadController@create')->name('lead.create');
    Route::post('/store', 'API\V1\LeadController@store')->name('lead.store');

    Route::post('/delete', 'API\V1\LeadController@delete')->name('lead.delete');

    Route::post('/files/store', 'API\V1\LeadController@fileStore')->name('lead.file.store');
    Route::get('/files/download', 'API\V1\LeadController@download')->name('lead.file.download');
    Route::get('/files/remove', 'API\V1\LeadController@deleteDocument')->name('lead.file.delete');
});
