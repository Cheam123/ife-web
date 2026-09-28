<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth']], function () {

    Route::get('/telegram', 'API\V1\TelegramController@telegram_message')->name('setting.telegram_message');
    Route::get('/telegram/test', 'API\V1\TelegramController@telegram_test')->name('setting.telegram_test');

    Route::get('/ontelegramwebhook', 'API\V1\TelegramController@turn_on_telegram_webhook')->name('setting.on_telegram_webhook');
    Route::get('/offtelegramwebhook', 'API\V1\TelegramController@turn_off_telegram_webhook')->name('setting.off_telegram_webhook');

});
