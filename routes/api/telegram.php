<?php

use Illuminate\Support\Facades\Route;

Route::post('/callback', 'API\V1\TelegramController@callback')->name('telegram.callback');
