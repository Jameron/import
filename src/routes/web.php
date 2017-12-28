<?php 

Route::group(['middleware' => ['web', 'auth', 'role:admin']], function () {
    Route::get(config('import.get_import_route'), 'Jameron\Import\Http\Controllers\ImportController@getImport');
    Route::post(config('import.post_import_route'), 'Jameron\Import\Http\Controllers\ImportController@postImport');
});
