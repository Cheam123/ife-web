<?php

use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth']], function () {

    Route::get('', 'API\V1\FormController@index')->name('form.index');
    Route::get('/entry', 'API\V1\FormController@entry')->name('form.entry');
    Route::get('/create', 'API\V1\FormController@create')->name('form.create'); 
    Route::post('/store', 'API\V1\FormController@store')->name('form.store');
    Route::get('/edit/{id}', 'API\V1\FormController@edit')->name('form.edit');
    Route::post('/update/{id}', 'API\V1\FormController@update')->name('form.update');
    Route::post('/delete', 'API\V1\FormController@destroy')->name('form.delete');
    Route::post('/toggle', 'API\V1\FormController@toggle')->name('form.toggle');
    Route::get('/preview/{id}', 'API\V1\FormController@preview')->name('form.preview');
    Route::get('/fill/{id}', 'API\V1\FormController@fill')->name('form.fill');
    Route::post('/submit', 'API\V1\FormController@submit')->name('form.submit');
    Route::get('/tasks', 'API\V1\FormController@myTasks')->name('form.tasks');

    /* Form groups — organise the form list. Containers only; a form's own
       "Who can submit" setting still decides who sees it. */
    Route::post('/groups', 'API\V1\FormController@storeGroup')->name('form.groups.store');
    Route::post('/groups/{id}/rename', 'API\V1\FormController@updateGroup')->name('form.groups.update');
    Route::post('/groups/{id}/delete', 'API\V1\FormController@destroyGroup')->name('form.groups.destroy');
    Route::post('/groups/reorder', 'API\V1\FormController@reorderForms')->name('form.groups.reorder');

    /* Records — the single object every form produces. A record is its first
       entry; forms that allow follow-ups gather more entries against it. */
    Route::get('/records', 'API\V1\FormController@records')->name('form.records.index');
    Route::get('/records/all', 'API\V1\FormController@allRecords')->name('form.records.all');
    Route::get('/records/export', 'API\V1\FormController@exportSubmissions')->name('form.records.export');
    Route::get('/records/{id}', 'API\V1\FormController@recordView')->name('form.records.show');
    Route::post('/records/{id}/close', 'API\V1\FormController@closeRecord')->name('form.records.close');
    Route::post('/records/{id}/reopen', 'API\V1\FormController@reopenRecord')->name('form.records.reopen');

    // Entry-level pages and actions (kept at these paths so notification deep
    // links and the RN app keep working).
    Route::get('/my-submissions/{id}', 'API\V1\FormController@viewSubmission')->name('form.submission.view');
    Route::get('/my-submissions/{id}/clone', 'API\V1\FormController@cloneSubmission')->name('form.submission.clone');
    Route::get('/my-submissions/{id}/edit', 'API\V1\FormController@editSubmission')->name('form.submission.edit');
    Route::post('/my-submissions/{id}/update', 'API\V1\FormController@updateSubmission')->name('form.submission.update');
    Route::post('/my-submissions/{id}/cancel', 'API\V1\FormController@cancelSubmission')->name('form.submission.cancel');

    Route::get('/admin/{id}', 'API\V1\FormController@adminViewSubmission')->name('form.admin.view');
    Route::post('/admin/{id}/approve', 'API\V1\FormController@approveSubmission')->name('form.admin.approve');
    Route::post('/admin/{id}/reject', 'API\V1\FormController@rejectSubmission')->name('form.admin.reject');
    Route::post('/admin/{id}/fill', 'API\V1\FormController@completeFillStage')->name('form.admin.fill');

});
